<?php

namespace Javeh\ClassValidator\Attributes;

use Attribute;
use Javeh\ClassValidator\Concerns\HandlesValidationMessage;
use Javeh\ClassValidator\Contracts\ValidationAttribute;

#[Attribute]
class Number implements ValidationAttribute
{
    use HandlesValidationMessage;

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

        $default = empty($constraints)
            ? "Der Wert muss eine gültige Zahl sein"
            : "Die Zahl muss " . implode(" und ", $constraints);

        $this->initializeErrorMessage($message, $default);
    }

    public function validate(mixed $value): bool
    {
        if ($value === null) {
            return true;
        }

        // Prüfe ob es eine Zahl ist
        if (!is_numeric($value)) {
            $this->replaceErrorMessage("Der Wert muss eine Zahl sein");
            return false;
        }

        $number = (float) $value;

        // Ganzzahl-Prüfung
        if ($this->integer && !is_int($number) && $number != (int)$number) {
            $this->replaceErrorMessage("Der Wert muss eine Ganzzahl sein");
            return false;
        }

        // Vorzeichen-Prüfung
        if ($this->positive && $number <= 0) {
            $this->replaceErrorMessage("Die Zahl muss positiv sein");
            return false;
        }
        if ($this->negative && $number >= 0) {
            $this->replaceErrorMessage("Die Zahl muss negativ sein");
            return false;
        }

        // Bereichs-Prüfung
        if ($this->min !== null && $number < $this->min) {
            $this->replaceErrorMessage("Die Zahl muss größer oder gleich {$this->min} sein");
            return false;
        }
        if ($this->max !== null && $number > $this->max) {
            $this->replaceErrorMessage("Die Zahl muss kleiner oder gleich {$this->max} sein");
            return false;
        }

        // Schrittweiten-Prüfung
        if ($this->step !== null) {
            $remainder = fmod($number, $this->step);
            // Berücksichtige Floating-Point-Ungenauigkeiten
            if (abs($remainder) > 0.000001 && abs($remainder - $this->step) > 0.000001) {
                $this->replaceErrorMessage("Die Zahl muss ein Vielfaches von {$this->step} sein");
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
