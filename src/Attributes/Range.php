<?php

namespace Javeh\ClassValidator\Attributes;

use Attribute;
use Javeh\ClassValidator\Concerns\HandlesValidationMessage;
use Javeh\ClassValidator\Contracts\ValidationAttribute;

#[Attribute]
class Range implements ValidationAttribute
{
    use HandlesValidationMessage;

    private string $errorMessage;

    public function __construct(
        private readonly int|float $min,
        private readonly int|float $max,
        ?string $message = null
    ) {
        $this->initializeErrorMessage($message, 'validation.range', [
            'min' => $this->min,
            'max' => $this->max,
        ]);
    }

    public function validate(mixed $value): bool
    {
        if ($value === null) {
            return true;
        }

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
