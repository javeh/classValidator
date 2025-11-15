<?php

namespace Javeh\ClassValidator\Attributes;

use Attribute;
use Javeh\ClassValidator\Contracts\ValidationAttribute;

#[Attribute]
class Length implements ValidationAttribute
{
    private string $errorMessage;
    private ?int $exactLength;

    public function __construct(
        public ?int $length = null,
        private readonly ?int $min = null,
        private readonly ?int $max = null,
        ?string $message = null
    ) {
        $this->exactLength = $length;
        
        if ($length !== null) {
            $this->errorMessage = $message ?? "Der Wert muss exakt {$length} Einheiten lang sein";
        } elseif ($min !== null && $max !== null) {
            $this->errorMessage = $message ?? "Der Wert muss zwischen {$min} und {$max} Einheiten lang sein";
        } elseif ($min !== null) {
            $this->errorMessage = $message ?? "Der Wert muss mindestens {$min} Einheiten lang sein";
        } elseif ($max !== null) {
            $this->errorMessage = $message ?? "Der Wert darf höchstens {$max} Einheiten lang sein";
        } else {
            throw new \InvalidArgumentException('Mindestens einer der Parameter length, min oder max muss gesetzt sein');
        }
    }

    public function validate(mixed $value): bool
    {
        $length = $this->getLength($value);
        if ($length === null) {
            $this->errorMessage = "Der Wert muss ein String, Array oder Countable sein";
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
        if (is_string($value)) {
            match($type) {
                'exact' => $this->errorMessage = "Der String muss exakt {$this->exactLength} Zeichen lang sein",
                'min' => $this->errorMessage = "Der String muss mindestens {$this->min} Zeichen lang sein",
                'max' => $this->errorMessage = "Der String darf höchstens {$this->max} Zeichen lang sein",
            };
        } elseif (is_array($value)) {
            match($type) {
                'exact' => $this->errorMessage = "Das Array muss exakt {$this->exactLength} Elemente enthalten",
                'min' => $this->errorMessage = "Das Array muss mindestens {$this->min} Elemente enthalten",
                'max' => $this->errorMessage = "Das Array darf höchstens {$this->max} Elemente enthalten",
            };
        } elseif ($value instanceof \Countable) {
            match($type) {
                'exact' => $this->errorMessage = "Die Sammlung muss exakt {$this->exactLength} Elemente enthalten",
                'min' => $this->errorMessage = "Die Sammlung muss mindestens {$this->min} Elemente enthalten",
                'max' => $this->errorMessage = "Die Sammlung darf höchstens {$this->max} Elemente enthalten",
            };
        }
    }

    public function getErrorMessage(): string
    {
        return $this->errorMessage;
    }
} 