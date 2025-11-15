<?php

namespace Javeh\ClassValidator\Attributes;

use Attribute;
use Javeh\ClassValidator\Concerns\HandlesValidationMessage;
use Javeh\ClassValidator\Contracts\ValidationAttribute;

#[Attribute]
class Regex implements ValidationAttribute
{
    use HandlesValidationMessage;

    private string $errorMessage;

    public function __construct(
        private readonly string $pattern,
        ?string $message = null
    ) {
        $this->initializeErrorMessage($message, "Der Wert entspricht nicht dem erforderlichen Muster");
    }

    public function validate(mixed $value): bool
    {
        if (is_string($value) && preg_match($this->pattern, $value)) {
            return true;
        }

        $this->replaceErrorMessage("Der Wert entspricht nicht dem erforderlichen Muster");
        return false;
    }

    public function getErrorMessage(): string
    {
        return $this->errorMessage;
    }
} 
