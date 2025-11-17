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
        ?string $max = null
    ) {
        $this->format = $this->assertValidFormat($format ?? 'Y-m-d');
        $this->minDate = $min ? $this->createBoundaryDate($min, 'Mindestdatum') : null;
        $this->maxDate = $max ? $this->createBoundaryDate($max, 'Maximaldatum') : null;

        if ($this->minDate && $this->maxDate && $this->minDate > $this->maxDate) {
            throw new \InvalidArgumentException('The minimum date may not be after the maximum date.');
        }

        $this->initializeErrorMessage('validation.date.invalid', [
            'format' => $this->format,
        ]);
    }

    public function validate(mixed $value): bool
    {
        if ($value === null) {
            return true;
        }

        if (!is_string($value)) {
            $this->replaceErrorMessage('validation.date.type', ['format' => $this->format]);
            return false;
        }

        $date = DateTime::createFromFormat($this->format, $value);
        if (!$date || $date->format($this->format) !== $value) {
            $this->replaceErrorMessage('validation.date.invalid', ['format' => $this->format]);
            return false;
        }

        if ($this->minDate && $date < $this->minDate) {
            $this->replaceErrorMessage('validation.date.after', [
                'date' => $this->minDate->format($this->format),
            ]);
            return false;
        }

        if ($this->maxDate && $date > $this->maxDate) {
            $this->replaceErrorMessage('validation.date.before', [
                'date' => $this->maxDate->format($this->format),
            ]);
            return false;
        }

        return true;
    }

    public function getErrorMessage(): string
    {
        return $this->errorMessage;
    }

    private function assertValidFormat(string $format): string
    {
        if (trim($format) === '') {
            throw new \InvalidArgumentException('Date format may not be empty.');
        }

        $probeValue = (new DateTime('now'))->format($format);
        $probeDate = DateTime::createFromFormat($format, $probeValue);

        if (!$probeDate || $probeDate->format($format) !== $probeValue) {
            throw new \InvalidArgumentException("Invalid date format: {$format}");
        }

        return $format;
    }

    private function createBoundaryDate(string $value, string $label): DateTime
    {
        $date = DateTime::createFromFormat($this->format, $value);

        if (!$date || $date->format($this->format) !== $value) {
            throw new \InvalidArgumentException("Invalid {$label}: {$value}");
        }

        return $date;
    }
}
