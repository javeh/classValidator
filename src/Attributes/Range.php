<?php

namespace Javeh\ClassValidator\Attributes;

use Attribute;
use Javeh\ClassValidator\Concerns\HandlesValidationMessage;
use Javeh\ClassValidator\Contracts\ValidationAttribute;
use Javeh\ClassValidator\Support\TranslationManager;
use Javeh\ClassValidator\ValidationContext;

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

    public function validate(mixed $value, ValidationContext $context): bool
    {
        if ($value === null) {
            return true;
        }

        if (is_numeric($value) && $value >= $this->min && $value <= $this->max) {
            return true;
        }

        // If the value is not numeric, or not within range, we need to set the error message.
        // The user's edit seems to imply a change in message key and arguments for non-numeric values.
        // Assuming 'validation.range.type' is for non-numeric values, and 'validation.range' for out-of-range numeric values.
        // However, the provided edit is syntactically incorrect and places the new call inside the 'return true' block.
        // Given the instruction "Pass context to replaceErrorMessage calls", and the structure of the edit,
        // it's interpreted as modifying the existing error message call to include context and potentially change the message key.

        // Original logic: if not numeric OR not in range, set 'validation.range' message.
        // User's edit suggests 'validation.range.type' with empty array and context.
        // This implies a distinction between type error and range error.
        // To make the edit syntactically correct and align with the instruction,
        // I will assume the user wants to pass context to the existing call,
        // and potentially change the message key if the value is not numeric.

        if (!is_numeric($value)) {
            $this->replaceErrorMessage('validation.range.type', [], $context);
        } else {
            $this->replaceErrorMessage('validation.range', [
                'min' => $this->min,
                'max' => $this->max,
            ], $context);
        }

        return false;
    }

    public function getErrorMessage(): string
    {
        return $this->errorMessage;
    }
}
