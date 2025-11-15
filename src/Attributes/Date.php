<?php

namespace Javeh\ClassValidator\Attributes;

use Attribute;
use DateTime;
use Javeh\ClassValidator\Concerns\HandlesValidationMessage;
use Javeh\ClassValidator\Contracts\ValidationAttribute;

#[Attribute]
class Date implements ValidationAttribute
{
    use HandlesValidationMessage;

    private string $errorMessage;
    private ?DateTime $minDate;
    private ?DateTime $maxDate;
    private string $format;

    public function __construct(
        ?string $format = 'Y-m-d',
        ?string $min = null,
        ?string $max = null,
        ?string $message = null
    ) {
        $this->format = $this->assertValidFormat($format ?? 'Y-m-d');
        $this->minDate = $min ? $this->createBoundaryDate($min, 'Mindestdatum') : null;
        $this->maxDate = $max ? $this->createBoundaryDate($max, 'Maximaldatum') : null;

        if ($this->minDate && $this->maxDate && $this->minDate > $this->maxDate) {
            throw new \InvalidArgumentException('Das Mindestdatum darf nicht nach dem Maximaldatum liegen');
        }

        $this->initializeErrorMessage($message, $this->generateDefaultMessage());
    }

    public function validate(mixed $value): bool
    {
        if ($value === null) {
            return true;
        }

        if (!is_string($value)) {
            $this->replaceErrorMessage("Der Wert muss ein Datum im Format {$this->format} sein");
            return false;
        }

        $date = DateTime::createFromFormat($this->format, $value);
        if (!$date || $date->format($this->format) !== $value) {
            $this->replaceErrorMessage("Der Wert muss ein gültiges Datum im Format {$this->format} sein");
            return false;
        }

        if ($this->minDate && $date < $this->minDate) {
            $this->replaceErrorMessage("Das Datum muss nach dem {$this->minDate->format($this->format)} liegen");
            return false;
        }

        if ($this->maxDate && $date > $this->maxDate) {
            $this->replaceErrorMessage("Das Datum muss vor dem {$this->maxDate->format($this->format)} liegen");
            return false;
        }

        return true;
    }

    private function generateDefaultMessage(): string
    {
        $constraints = ["ein gültiges Datum im Format {$this->format} sein"];
        
        if ($this->minDate && $this->maxDate) {
            $constraints[] = "zwischen {$this->minDate->format($this->format)} und {$this->maxDate->format($this->format)} liegen";
        } elseif ($this->minDate) {
            $constraints[] = "nach dem {$this->minDate->format($this->format)} liegen";
        } elseif ($this->maxDate) {
            $constraints[] = "vor dem {$this->maxDate->format($this->format)} liegen";
        }

        return "Das Datum muss " . implode(" und ", $constraints);
    }

    public function getErrorMessage(): string
    {
        return $this->errorMessage;
    }

    private function assertValidFormat(string $format): string
    {
        if (trim($format) === '') {
            throw new \InvalidArgumentException('Das Datumsformat darf nicht leer sein');
        }

        $probeValue = (new DateTime('now'))->format($format);
        $probeDate = DateTime::createFromFormat($format, $probeValue);

        if (!$probeDate || $probeDate->format($format) !== $probeValue) {
            throw new \InvalidArgumentException("Ungültiges Datumsformat: {$format}");
        }

        return $format;
    }

    private function createBoundaryDate(string $value, string $label): DateTime
    {
        $date = DateTime::createFromFormat($this->format, $value);

        if (!$date || $date->format($this->format) !== $value) {
            throw new \InvalidArgumentException("Ungültiges {$label}: {$value}");
        }

        return $date;
    }
}
