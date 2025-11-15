<?php

namespace Javeh\ClassValidator\Contracts;

interface ValidationAttribute
{
    public function validate(mixed $value): bool;

    public function getErrorMessage(): string;
} 