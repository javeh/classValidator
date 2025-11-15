<?php

namespace Javeh\ClassValidator\Attributes;

use Attribute;
use Javeh\ClassValidator\Concerns\HandlesValidationMessage;
use Javeh\ClassValidator\Contracts\ValidationAttribute;

#[Attribute]
class Text implements ValidationAttribute
{
    use HandlesValidationMessage;

    private string $errorMessage;
    private ?int $exactLength;

    public function __construct(
        ?int $length = null,
        private readonly ?int $min = null,
        private readonly ?int $max = null,
        private readonly ?string $pattern = null,
        ?string $message = null
    ) {
        $this->exactLength = $length;

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

        $default = empty($constraints)
            ? "Der Text muss gültig sein"
            : "Der Text muss " . implode(" und ", $constraints);

        $this->initializeErrorMessage($message, $default);
    }

    public function validate(mixed $value): bool
    {
        if (!is_string($value)) {
            $this->replaceErrorMessage("Der Wert muss ein Text sein");
            return false;
        }

        // Prüfung der exakten Länge
        if ($this->exactLength !== null && strlen($value) !== $this->exactLength) {
            $this->replaceErrorMessage("Der Text muss exakt {$this->exactLength} Zeichen lang sein");
            return false;
        }

        // Prüfung der Mindestlänge
        if ($this->min !== null && strlen($value) < $this->min) {
            $this->replaceErrorMessage("Der Text muss mindestens {$this->min} Zeichen lang sein");
            return false;
        }

        // Prüfung der Maximallänge
        if ($this->max !== null && strlen($value) > $this->max) {
            $this->replaceErrorMessage("Der Text darf höchstens {$this->max} Zeichen lang sein");
            return false;
        }

        // Prüfung des Regex-Patterns
        if ($this->pattern !== null && !preg_match($this->pattern, $value)) {
            $this->replaceErrorMessage("Der Text entspricht nicht dem erforderlichen Muster");
            return false;
        }

        return true;
    }

    public function getErrorMessage(): string
    {
        return $this->errorMessage;
    }
} 
