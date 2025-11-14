<?php

namespace Idalabs\Validation\Attributes;

use Attribute;
use Idalabs\Validation\Contracts\ValidationAttribute;

#[Attribute]
class Url implements ValidationAttribute
{
    private string $errorMessage;

    public function __construct(?string $message = null)
    {
        $this->errorMessage = $message ?? "Der Wert muss eine gültige URL sein";
    }

    public function validate(mixed $value): bool
    {
        if (filter_var($value, FILTER_VALIDATE_URL)) {
            return true;
        }

        $this->errorMessage = "Der Wert muss eine gültige URL sein";
        return false;
    }

    public function getErrorMessage(): string
    {
        return $this->errorMessage;
    }
} 