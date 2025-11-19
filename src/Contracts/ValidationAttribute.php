<?php

namespace Javeh\ClassValidator\Contracts;

use Javeh\ClassValidator\ValidationContext;

interface ValidationAttribute
{
    public function validate(mixed $value, ValidationContext $context): bool;

    public function getErrorMessage(): string;
}