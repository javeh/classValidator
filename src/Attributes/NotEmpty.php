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

    public function __construct(?string $message = null)
    {
        $this->initializeErrorMessage($message, "Der Wert darf nicht leer sein");
    }

    public function validate(mixed $value): bool
    {
        if (!empty($value)) {
            return true;
        }

        $this->replaceErrorMessage("Der Wert darf nicht leer sein");
        return false;
    }

    public function getErrorMessage(): string
    {
        return $this->errorMessage;
    }
} 
