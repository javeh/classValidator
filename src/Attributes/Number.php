<?php

namespace Idalabs\Validation\Attributes;

use Attribute;
use Idalabs\Validation\Contracts\ValidationAttribute;

#[Attribute]
class Number implements ValidationAttribute
{
    private string $errorMessage;

    public function __construct(
        private readonly ?float $min = null,
        private readonly ?float $max = null,
        private readonly ?bool $integer = false,
        private readonly ?bool $positive = false,
        private readonly ?bool $negative = false,
        private readonly ?float $step = null,
        ?string $message = null
    ) {
        if ($positive && $negative) {
            throw new \InvalidArgumentException('Eine Zahl kann nicht gleichzeitig positiv und negativ sein');
        }

        if ($message !== null) {
            $this->errorMessage = $message;
            return;
        }

        // Standardfehlermeldung basierend auf den Einschränkungen
        $constraints = [];
        
        if ($integer) {
            $constraints[] = "eine Ganzzahl sein";
        }
        if ($positive) {
            $constraints[] = "positiv sein";
        }
        if ($negative) {
            $constraints[] = "negativ sein";
        }
        if ($min !== null && $max !== null) {
            $constraints[] = "zwischen {$min} und {$max} liegen";
        } elseif ($min !== null) {
            $constraints[] = "größer oder gleich {$min} sein";
        } elseif ($max !== null) {
            $constraints[] = "kleiner oder gleich {$max} sein";
        }
        if ($step !== null) {
            $constraints[] = "ein Vielfaches von {$step} sein";
        }

        if (empty($constraints)) {
            $this->errorMessage = "Der Wert muss eine gültige Zahl sein";
        } else {
            $this->errorMessage = "Die Zahl muss " . implode(" und ", $constraints);
        }
    }

    public function validate(mixed $value): bool
    {
        // Prüfe ob es eine Zahl ist
        if (!is_numeric($value)) {
            $this->errorMessage = "Der Wert muss eine Zahl sein";
            return false;
        }

        $number = (float) $value;

        // Ganzzahl-Prüfung
        if ($this->integer && !is_int($number) && $number != (int)$number) {
            $this->errorMessage = "Der Wert muss eine Ganzzahl sein";
            return false;
        }

        // Vorzeichen-Prüfung
        if ($this->positive && $number <= 0) {
            $this->errorMessage = "Die Zahl muss positiv sein";
            return false;
        }
        if ($this->negative && $number >= 0) {
            $this->errorMessage = "Die Zahl muss negativ sein";
            return false;
        }

        // Bereichs-Prüfung
        if ($this->min !== null && $number < $this->min) {
            $this->errorMessage = "Die Zahl muss größer oder gleich {$this->min} sein";
            return false;
        }
        if ($this->max !== null && $number > $this->max) {
            $this->errorMessage = "Die Zahl muss kleiner oder gleich {$this->max} sein";
            return false;
        }

        // Schrittweiten-Prüfung
        if ($this->step !== null) {
            $remainder = fmod($number, $this->step);
            // Berücksichtige Floating-Point-Ungenauigkeiten
            if (abs($remainder) > 0.000001 && abs($remainder - $this->step) > 0.000001) {
                $this->errorMessage = "Die Zahl muss ein Vielfaches von {$this->step} sein";
                return false;
            }
        }

        return true;
    }

    public function getErrorMessage(): string
    {
        return $this->errorMessage;
    }
} 