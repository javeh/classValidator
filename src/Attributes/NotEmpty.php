<?php

namespace Javeh\ClassValidator\Attributes;

use Attribute;
use Javeh\ClassValidator\Concerns\HandlesValidationMessage;
use Javeh\ClassValidator\Contracts\ValidationAttribute;

#[Attribute]
class NotEmpty implements ValidationAttribute
{
    use HandlesValidationMessage;

    private string $errorMessage;

    public function __construct()
    {
        $this->initializeErrorMessage('validation.not_empty');
    }

    public function validate(mixed $value): bool
    {
        if (!empty($value)) {
            return true;
        }

        $this->replaceErrorMessage('validation.not_empty');
        return false;
    }

    public function getErrorMessage(): string
    {
        return $this->errorMessage;
    }
} 
