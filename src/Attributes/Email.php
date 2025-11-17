<?php

namespace Javeh\ClassValidator\Attributes;

use Attribute;
use Javeh\ClassValidator\Concerns\HandlesValidationMessage;
use Javeh\ClassValidator\Contracts\ValidationAttribute;

#[Attribute]
class Email implements ValidationAttribute
{
    use HandlesValidationMessage;

    private string $errorMessage;
    
    public function __construct()
    {
        $this->initializeErrorMessage('validation.email');
    }

    public function validate(mixed $value): bool
    {
        if ($value === null) {
            return true;
        }

        if (filter_var($value, FILTER_VALIDATE_EMAIL)) {
            return true;
        }

        $this->replaceErrorMessage('validation.email');
        return false;
    }

    public function getErrorMessage(): string
    {
        return $this->errorMessage;
    }
} 
