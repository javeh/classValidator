<?php

namespace Javeh\ClassValidator\Attributes;

use Attribute;
use Javeh\ClassValidator\Concerns\HandlesValidationMessage;
use Javeh\ClassValidator\Contracts\ValidationAttribute;
use Javeh\ClassValidator\Support\TranslationManager;

#[Attribute]
class Range implements ValidationAttribute
{
    use HandlesValidationMessage;

    private string $errorMessage;

    public function __construct(
        private readonly int|float $min,
        private readonly int|float $max
    ) {
        if ($this->max < $this->min) {
            throw new \InvalidArgumentException(
                TranslationManager::get()->translate('validation.config.range.bounds')
            );
        }

        $this->initializeErrorMessage('validation.range', [
            'min' => $this->min,
            'max' => $this->max,
        ]);
    }

    public function validate(mixed $value): bool
    {
        if ($value === null) {
            return true;
        }

        if (is_numeric($value) && $value >= $this->min && $value <= $this->max) {
            return true;
        }

        $this->replaceErrorMessage('validation.range', [
            'min' => $this->min,
            'max' => $this->max,
        ]);
        return false;
    }

    public function getErrorMessage(): string
    {
        return $this->errorMessage;
    }
} 
