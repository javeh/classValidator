<?php

namespace Javeh\ClassValidator\Attributes;

use Attribute;
use Javeh\ClassValidator\Contracts\ValidationAttribute;

#[Attribute]
class Text implements ValidationAttribute
{
    private string $errorMessage;
    private ?int $exactLength;

    public function __construct(
        int $length = null,
        private readonly ?int $min = null,
        private readonly ?int $max = null,
        private readonly ?string $pattern = null,
        ?string $message = null
    ) {
        $this->exactLength = $length;
        
        if (!is_null($message)) {
            $this->errorMessage = $message;
            return;
        }

        // Standard-Fehlermeldung basierend auf den gesetzten Einschränkungen
        $constraints = [];
        
        if ($length !== null) {
            $constraints[] = "exakt {$length} Zeichen lang sein";
        }
        if ($min !== null && $max !== null) {
            $constraints[] = "zwischen {$min} und {$max} Zeichen lang sein";
        } elseif ($min !== null) {
            $constraints[] = "mindestens {$min} Zeichen lang sein";
        } elseif ($max !== null) {
            $constraints[] = "höchstens {$max} Zeichen lang sein";
        }
        if ($pattern !== null) {
            $constraints[] = "dem vorgegebenen Muster entsprechen";
        }

        if (empty($constraints)) {
            $this->errorMessage = "Der Wert muss ein gültiger String sein";
        } else {
            $this->errorMessage = "Der String muss " . implode(" und ", $constraints);
        }
    }

    public function validate(mixed $value): bool
    {
        if (!is_string($value)) {
            $this->errorMessage = "Der Wert muss ein String sein";
            return false;
        }

        // Prüfung der exakten Länge
        if ($this->exactLength !== null && strlen($value) !== $this->exactLength) {
            $this->errorMessage = "Der String muss exakt {$this->exactLength} Zeichen lang sein";
            return false;
        }

        // Prüfung der Mindestlänge
        if ($this->min !== null && strlen($value) < $this->min) {
            $this->errorMessage = "Der String muss mindestens {$this->min} Zeichen lang sein";
            return false;
        }

        // Prüfung der Maximallänge
        if ($this->max !== null && strlen($value) > $this->max) {
            $this->errorMessage = "Der String darf höchstens {$this->max} Zeichen lang sein";
            return false;
        }

        // Prüfung des Regex-Patterns
        if ($this->pattern !== null && !preg_match($this->pattern, $value)) {
            $this->errorMessage = "Der String entspricht nicht dem erforderlichen Muster";
            return false;
        }

        return true;
    }

    public function getErrorMessage(): string
    {
        return $this->errorMessage;
    }
} 