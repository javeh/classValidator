<?php

namespace Javeh\ClassValidator\Attributes;

use Attribute;
use Javeh\ClassValidator\Concerns\HandlesValidationMessage;
use Javeh\ClassValidator\Contracts\ValidationAttribute;
use Javeh\ClassValidator\ValidationContext;

#[Attribute]
class Url implements ValidationAttribute
{
    use HandlesValidationMessage;

    private string $errorMessage;

    public function __construct()
    {
        $this->initializeErrorMessage('validation.url');
    }

    public function validate(mixed $value, ValidationContext $context): bool
    {
        if ($value === null) {
            return true;
        }

        if (filter_var($value, FILTER_VALIDATE_URL)) {
            return true;
        }

        $this->replaceErrorMessage('validation.url', [], $context);
        return false;
    }

    public function getErrorMessage(): string
    {
        return $this->errorMessage;
    }
}
