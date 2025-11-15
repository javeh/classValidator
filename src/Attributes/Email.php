<?php

namespace Javeh\ClassValidator\Attributes;

use Attribute;
use Javeh\ClassValidator\Contracts\ValidationAttribute;

#[Attribute]
class Email implements ValidationAttribute
{
    private string $errorMessage;
    
    public function __construct(string $errorMessage = 'Invalid email format')
    {
        $this->errorMessage = $errorMessage;
    }

    public function validate(mixed $value): bool
    {
        if (filter_var($value, FILTER_VALIDATE_EMAIL)) {
            return true;
        }

        $this->errorMessage = "Der Wert muss eine gültige E-Mail-Adresse sein";
        return false;
    }

    public function getErrorMessage(): string
    {
        return $this->errorMessage;
    }
} 