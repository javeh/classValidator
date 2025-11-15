<?php

namespace Javeh\ClassValidator\Attributes;

use Attribute;
use Javeh\ClassValidator\Concerns\HandlesValidationMessage;
use Javeh\ClassValidator\Contracts\ValidationAttribute;

#[Attribute]
class Length implements ValidationAttribute
{
    use HandlesValidationMessage;

    private string $errorMessage;
    private ?int $exactLength;

    public function __construct(
        public ?int $length = null,
        private readonly ?int $min = null,
        private readonly ?int $max = null,
        ?string $message = null
    ) {
        $this->exactLength = $length;
        
        if ($length === null && $min === null && $max === null) {
            throw new \InvalidArgumentException('Mindestens einer der Parameter length, min oder max muss gesetzt sein');
        }

        $this->initializeErrorMessage($message, 'validation.length.type');
    }

    public function validate(mixed $value): bool
    {
        if ($value === null) {
            return true;
        }

        $length = $this->getLength($value);
        if ($length === null) {
            $this->replaceErrorMessage('validation.length.type');
            return false;
        }

        // Prüfung der exakten Länge
        if ($this->exactLength !== null) {
            if ($length !== $this->exactLength) {
                $this->setSpecificErrorMessage($value);
                return false;
            }
            return true;
        }

        // Prüfung der Min/Max Länge
        if ($this->min !== null && $length < $this->min) {
            $this->setSpecificErrorMessage($value, 'min');
            return false;
        }

        if ($this->max !== null && $length > $this->max) {
            $this->setSpecificErrorMessage($value, 'max');
            return false;
        }

        return true;
    }

    private function getLength(mixed $value): ?int
    {
        if (is_string($value)) {
            return strlen($value);
        }
        if (is_array($value)) {
            return count($value);
        }
        if ($value instanceof \Countable) {
            return count($value);
        }
        return null;
    }

    private function setSpecificErrorMessage(mixed $value, ?string $type = 'exact'): void
    {
        $message = null;

        if (is_string($value)) {
            $key = match($type) {
                'exact' => 'validation.length.text_exact',
                'min' => 'validation.length.text_min',
                'max' => 'validation.length.text_max',
                default => null,
            };
            $context = [
                'exact' => $this->exactLength,
                'min' => $this->min,
                'max' => $this->max,
            ];
        } elseif (is_array($value)) {
            $key = match($type) {
                'exact' => 'validation.length.array_exact',
                'min' => 'validation.length.array_min',
                'max' => 'validation.length.array_max',
                default => null,
            };
            $context = [
                'exact' => $this->exactLength,
                'min' => $this->min,
                'max' => $this->max,
            ];
        } elseif ($value instanceof \Countable) {
            $key = match($type) {
                'exact' => 'validation.length.collection_exact',
                'min' => 'validation.length.collection_min',
                'max' => 'validation.length.collection_max',
                default => null,
            };
            $context = [
                'exact' => $this->exactLength,
                'min' => $this->min,
                'max' => $this->max,
            ];
        } else {
            $key = null;
            $context = [];
        }

        if ($key !== null) {
            $this->replaceErrorMessage($key, array_filter($context, fn($value) => $value !== null));
        }
    }

    public function getErrorMessage(): string
    {
        return $this->errorMessage;
    }
} 
