<?php

namespace Javeh\ClassValidator\Attributes;

use Attribute;
use Javeh\ClassValidator\Contracts\ValidationAttribute;
use Javeh\ClassValidator\ValidationContext;

#[Attribute]
class Id implements ValidationAttribute
{
    private Number $numberValidator;

    public function __construct()
    {
        $this->numberValidator = new Number(integer: true, positive: true);
    }

    public function validate(mixed $value, ValidationContext $context): bool
    {
        return $this->numberValidator->validate($value, $context);
    }

    public function getErrorMessage(): string
    {
        return $this->numberValidator->getErrorMessage();
    }
}
