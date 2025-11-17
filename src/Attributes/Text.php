<?php

namespace Javeh\ClassValidator\Attributes;

use Attribute;
use Javeh\ClassValidator\Concerns\HandlesValidationMessage;
use Javeh\ClassValidator\Contracts\ValidationAttribute;

#[Attribute]
class Text implements ValidationAttribute
{
    use HandlesValidationMessage;

    private string $errorMessage;
    private ?Length $lengthValidator = null;

    public function __construct(
        ?int $length = null,
        ?int $min = null,
        ?int $max = null,
        private readonly ?string $pattern = null
    ) {
        if ($length !== null || $min !== null || $max !== null) {
            $this->lengthValidator = new Length(length: $length, min: $min, max: $max);
        }

        $this->initializeErrorMessage('validation.text.valid');
    }

    public function validate(mixed $value): bool
    {
        if ($value === null) {
            return true;
        }

        if (!is_string($value)) {
            $this->replaceErrorMessage('validation.text.type');
            return false;
        }

        if ($this->lengthValidator !== null && !$this->lengthValidator->validate($value)) {
            $this->adoptErrorMessage($this->lengthValidator->getErrorMessage());
            return false;
        }

        // Prüfung des Regex-Patterns
        if ($this->pattern !== null && !preg_match($this->pattern, $value)) {
            $this->replaceErrorMessage('validation.text.pattern');
            return false;
        }

        return true;
    }

    public function getErrorMessage(): string
    {
        return $this->errorMessage;
    }
} 
