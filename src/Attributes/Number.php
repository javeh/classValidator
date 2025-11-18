<?php

namespace Javeh\ClassValidator\Attributes;

use Attribute;
use Javeh\ClassValidator\Concerns\HandlesValidationMessage;
use Javeh\ClassValidator\Contracts\ValidationAttribute;
use Javeh\ClassValidator\Support\TranslationManager;

#[Attribute]
class Number implements ValidationAttribute
{
    use HandlesValidationMessage;

    private string $errorMessage;

    public function __construct(
        private readonly ?float $min = null,
        private readonly ?float $max = null,
        private readonly ?bool $integer = false,
        private readonly ?bool $positive = false,
        private readonly ?bool $negative = false,
        private readonly ?float $step = null
    ) {
        if ($positive && $negative) {
            throw new \InvalidArgumentException(
                TranslationManager::get()->translate('validation.config.number.sign_conflict')
            );
        }

        if ($step !== null && $step <= 0) {
            throw new \InvalidArgumentException(
                TranslationManager::get()->translate('validation.config.number.step_positive')
            );
        }

        if ($min !== null && $max !== null && $min > $max) {
            throw new \InvalidArgumentException(
                TranslationManager::get()->translate('validation.config.number.bounds', [
                    'min' => $min,
                    'max' => $max,
                ])
            );
        }

        $this->initializeErrorMessage('validation.number.type');
    }

    public function validate(mixed $value): bool
    {
        if ($value === null) {
            return true;
        }

        // Prüfe ob es eine Zahl ist
        if (!is_numeric($value)) {
            $this->replaceErrorMessage('validation.number.type');
            return false;
        }

        $number = (float) $value;

        // Ganzzahl-Prüfung
        if ($this->integer && !is_int($number) && $number != (int)$number) {
            $this->replaceErrorMessage('validation.number.integer');
            return false;
        }

        // Vorzeichen-Prüfung
        if ($this->positive && $number <= 0) {
            $this->replaceErrorMessage('validation.number.positive');
            return false;
        }
        if ($this->negative && $number >= 0) {
            $this->replaceErrorMessage('validation.number.negative');
            return false;
        }

        // Bereichs-Prüfung
        if ($this->min !== null && $number < $this->min) {
            $this->replaceErrorMessage('validation.number.min', ['min' => $this->min]);
            return false;
        }
        if ($this->max !== null && $number > $this->max) {
            $this->replaceErrorMessage('validation.number.max', ['max' => $this->max]);
            return false;
        }

        // Schrittweiten-Prüfung
        if ($this->step !== null) {
            $remainder = fmod($number, $this->step);
            // Berücksichtige Floating-Point-Ungenauigkeiten
            if (abs($remainder) > 0.000001 && abs($remainder - $this->step) > 0.000001) {
                $this->replaceErrorMessage('validation.number.step', ['step' => $this->step]);
                return false;
            }
        }

        return true;
    }

    public function getErrorMessage(): string
    {
        return $this->errorMessage;
    }
} 
