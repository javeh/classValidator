<?php

namespace Javeh\ClassValidator;

use JsonSerializable;

class ValidationResult implements JsonSerializable
{
    public function __construct(private readonly array $errors)
    {
    }

    public function isValid(): bool
    {
        return empty($this->errors);
    }

    public function isInvalid(): bool
    {
        return !$this->isValid();
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function firstError(): ?string
    {
        if ($this->isValid()) {
            return null;
        }

        $firstField = array_key_first($this->errors);
        return $this->errors[$firstField][0] ?? null;
    }

    public function jsonSerialize(): array
    {
        return [
            'isValid' => $this->isValid(),
            'errors' => $this->errors,
        ];
    }
}
