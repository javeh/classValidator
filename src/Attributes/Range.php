<?php

namespace Javeh\ClassValidator\Attributes;

use Attribute;
use Javeh\ClassValidator\Contracts\ValidationAttribute;

#[Attribute]
class Range implements ValidationAttribute
{
    private string $errorMessage;

    public function __construct(
        private readonly int|float $min,
        private readonly int|float $max,
        ?string $message = null
    ) {
        $this->errorMessage = $message ?? "Der Wert muss zwischen {$this->min} und {$this->max} liegen";
    }

    public function validate(mixed $value): bool
    {
        if (is_numeric($value) && $value >= $this->min && $value <= $this->max) {
            return true;
        }

        $this->errorMessage = "Der Wert muss zwischen {$this->min} und {$this->max} liegen";
        return false;
    }

    public function getErrorMessage(): string
    {
        return $this->errorMessage;
    }
} 