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
    
    public function __construct(?string $message = null)
    {
        $this->initializeErrorMessage($message, "Der Wert muss eine gültige E-Mail-Adresse sein");
    }

    public function validate(mixed $value): bool
    {
        if ($value === null) {
            return true;
        }

        if (filter_var($value, FILTER_VALIDATE_EMAIL)) {
            return true;
        }

        $this->replaceErrorMessage("Der Wert muss eine gültige E-Mail-Adresse sein");
        return false;
    }

    public function getErrorMessage(): string
    {
        return $this->errorMessage;
    }
} 
