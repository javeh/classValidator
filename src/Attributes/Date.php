<?php

namespace Javeh\ClassValidator\Attributes;

use Attribute;
use DateTime;
use Javeh\ClassValidator\Concerns\HandlesValidationMessage;
use Javeh\ClassValidator\Contracts\ValidationAttribute;
use Javeh\ClassValidator\Support\TranslationManager;

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
        $this->minDate = $min ? $this->createBoundaryDate($min, 'validation.config.date.min_label') : null;
        $this->maxDate = $max ? $this->createBoundaryDate($max, 'validation.config.date.max_label') : null;

        if ($this->minDate && $this->maxDate && $this->minDate > $this->maxDate) {
            throw new \InvalidArgumentException(
                TranslationManager::get()->translate('validation.config.date.bounds', [
                    'min' => $this->minDate->format($this->format),
                    'max' => $this->maxDate->format($this->format),
                ])
            );
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
            throw new \InvalidArgumentException(
                TranslationManager::get()->translate('validation.config.date.format_empty')
            );
        }

        $probeValue = (new DateTime('now'))->format($format);
        $probeDate = DateTime::createFromFormat($format, $probeValue);

        if (!$probeDate || $probeDate->format($format) !== $probeValue) {
            throw new \InvalidArgumentException(
                TranslationManager::get()->translate('validation.config.date.format_invalid', [
                    'format' => $format,
                ])
            );
        }

        return $format;
    }

    private function createBoundaryDate(string $value, string $labelKey): DateTime
    {
        $label = TranslationManager::get()->translate($labelKey);
        $date = DateTime::createFromFormat($this->format, $value);

        if (!$date || $date->format($this->format) !== $value) {
            throw new \InvalidArgumentException(
                TranslationManager::get()->translate('validation.config.date.boundary', [
                    'label' => $label,
                    'value' => $value,
                    'format' => $this->format,
                ])
            );
        }

        return $date;
    }
}
