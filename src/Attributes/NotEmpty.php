<?php

namespace Javeh\ClassValidator\Attributes;

use Attribute;
use Javeh\ClassValidator\Contracts\ValidationAttribute;

#[Attribute]
class NotEmpty implements ValidationAttribute
{
    private string $errorMessage;

    public function __construct(?string $message = null)
    {
        $this->errorMessage = $message ?? "Der Wert darf nicht leer sein";
    }

    public function validate(mixed $value): bool
    {
        if (!empty($value)) {
            return true;
        }

        $this->errorMessage = "Der Wert darf nicht leer sein";
        return false;
    }

    public function getErrorMessage(): string
    {
        return $this->errorMessage;
    }
} 