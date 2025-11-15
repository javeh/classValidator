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
        
        if ($length !== null) {
            $defaultMessage = "Der Wert muss exakt {$length} Einheiten lang sein";
        } elseif ($min !== null && $max !== null) {
            $defaultMessage = "Der Wert muss zwischen {$min} und {$max} Einheiten lang sein";
        } elseif ($min !== null) {
            $defaultMessage = "Der Wert muss mindestens {$min} Einheiten lang sein";
        } elseif ($max !== null) {
            $defaultMessage = "Der Wert darf höchstens {$max} Einheiten lang sein";
        } else {
            throw new \InvalidArgumentException('Mindestens einer der Parameter length, min oder max muss gesetzt sein');
        }

        $this->initializeErrorMessage($message, $defaultMessage);
    }

    public function validate(mixed $value): bool
    {
        $length = $this->getLength($value);
        if ($length === null) {
            $this->replaceErrorMessage("Der Wert muss ein Text, ein Array oder eine zählbare Sammlung sein");
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
            $message = match($type) {
                'exact' => "Der Text muss exakt {$this->exactLength} Zeichen lang sein",
                'min' => "Der Text muss mindestens {$this->min} Zeichen lang sein",
                'max' => "Der Text darf höchstens {$this->max} Zeichen lang sein",
                default => null,
            };
        } elseif (is_array($value)) {
            $message = match($type) {
                'exact' => "Das Array muss exakt {$this->exactLength} Elemente enthalten",
                'min' => "Das Array muss mindestens {$this->min} Elemente enthalten",
                'max' => "Das Array darf höchstens {$this->max} Elemente enthalten",
                default => null,
            };
        } elseif ($value instanceof \Countable) {
            $message = match($type) {
                'exact' => "Die Sammlung muss exakt {$this->exactLength} Elemente enthalten",
                'min' => "Die Sammlung muss mindestens {$this->min} Elemente enthalten",
                'max' => "Die Sammlung darf höchstens {$this->max} Elemente enthalten",
                default => null,
            };
        }

        if ($message !== null) {
            $this->replaceErrorMessage($message);
        }
    }

    public function getErrorMessage(): string
    {
        return $this->errorMessage;
    }
} 
