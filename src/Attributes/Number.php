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
        private readonly ?float $step = null
    ) {
        if ($positive && $negative) {
            throw new \InvalidArgumentException('Eine Zahl kann nicht gleichzeitig positiv und negativ sein');
        }

        if ($step !== null && $step <= 0) {
            throw new \InvalidArgumentException('Die Schrittweite muss größer als 0 sein');
        }

        if ($min !== null && $max !== null && $min > $max) {
            throw new \InvalidArgumentException('Der Mindestwert darf nicht größer als der Maximalwert sein');
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

        $this->initializeErrorMessage('validation.number.type');
    }

    public function validate(mixed $value): bool
    {
        if ($value === null) {
            return true;
        }

        // Prüfe ob es eine Zahl ist
        if (!is_numeric($value)) {
            $this->replaceErrorMessage('validation.number.type');
            return false;
        }

        $number = (float) $value;

        // Ganzzahl-Prüfung
        if ($this->integer && !is_int($number) && $number != (int)$number) {
            $this->replaceErrorMessage('validation.number.integer');
            return false;
        }

        // Vorzeichen-Prüfung
        if ($this->positive && $number <= 0) {
            $this->replaceErrorMessage('validation.number.positive');
            return false;
        }
        if ($this->negative && $number >= 0) {
            $this->replaceErrorMessage('validation.number.negative');
            return false;
        }

        // Bereichs-Prüfung
        if ($this->min !== null && $number < $this->min) {
            $this->replaceErrorMessage('validation.number.min', ['min' => $this->min]);
            return false;
        }
        if ($this->max !== null && $number > $this->max) {
            $this->replaceErrorMessage('validation.number.max', ['max' => $this->max]);
            return false;
        }

        // Schrittweiten-Prüfung
        if ($this->step !== null) {
            $remainder = fmod($number, $this->step);
            // Berücksichtige Floating-Point-Ungenauigkeiten
            if (abs($remainder) > 0.000001 && abs($remainder - $this->step) > 0.000001) {
                $this->replaceErrorMessage('validation.number.step', ['step' => $this->step]);
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
