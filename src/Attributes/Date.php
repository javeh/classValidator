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

    public function __construct(
        private readonly ?string $format = 'Y-m-d',
        ?string $min = null,
        ?string $max = null,
        ?string $message = null
    ) {
        $this->minDate = $min ? DateTime::createFromFormat($format, $min) : null;
        $this->maxDate = $max ? DateTime::createFromFormat($format, $max) : null;

        if ($min && !$this->minDate) {
            throw new \InvalidArgumentException("Ungültiges Mindestdatum: {$min}");
        }
        if ($max && !$this->maxDate) {
            throw new \InvalidArgumentException("Ungültiges Maximaldatum: {$max}");
        }

        if ($this->minDate && $this->maxDate && $this->minDate > $this->maxDate) {
            throw new \InvalidArgumentException('Das Mindestdatum darf nicht nach dem Maximaldatum liegen');
        }

        $this->initializeErrorMessage($message, $this->generateDefaultMessage());
    }

    public function validate(mixed $value): bool
    {
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
} 
