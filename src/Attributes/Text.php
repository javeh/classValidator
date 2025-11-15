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
    private ?Length $lengthValidator = null;

    public function __construct(
        ?int $length = null,
        private readonly ?int $min = null,
        private readonly ?int $max = null,
        private readonly ?string $pattern = null,
        ?string $message = null
    ) {
        if ($length !== null || $min !== null || $max !== null) {
            $this->lengthValidator = new Length(length: $length, min: $min, max: $max);
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

        $default = empty($constraints)
            ? "Der Text muss gültig sein"
            : "Der Text muss " . implode(" und ", $constraints);

        $this->initializeErrorMessage($message, $default);
    }

    public function validate(mixed $value): bool
    {
        if ($value === null) {
            return true;
        }

        if (!is_string($value)) {
            $this->replaceErrorMessage("Der Wert muss ein Text sein");
            return false;
        }

        if ($this->lengthValidator !== null && !$this->lengthValidator->validate($value)) {
            $this->replaceErrorMessage($this->lengthValidator->getErrorMessage());
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
