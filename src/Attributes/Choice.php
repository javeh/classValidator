<?php

namespace Javeh\ClassValidator\Attributes;

use Attribute;
use Javeh\ClassValidator\Concerns\HandlesValidationMessage;
use Javeh\ClassValidator\Contracts\ValidationAttribute;

#[Attribute]
class Choice implements ValidationAttribute
{
    use HandlesValidationMessage;

    private string $errorMessage;
    private array $choices;

    public function __construct(
        array $choices,
        private readonly bool $strict = true,
        private readonly bool $multiple = false,
        ?string $message = null
    ) {
        if (empty($choices)) {
            throw new \InvalidArgumentException('Die Liste der Auswahlmöglichkeiten darf nicht leer sein');
        }
        
        $this->choices = array_values($choices);
        $this->initializeErrorMessage($message, $this->generateDefaultMessage());
    }

    public function validate(mixed $value): bool
    {
        if ($this->multiple) {
            if (!is_array($value)) {
                $this->replaceErrorMessage("Der Wert muss ein Array sein");
                return false;
            }

            foreach ($value as $item) {
                if (!$this->validateSingleValue($item)) {
                    return false;
                }
            }
            return true;
        }

        return $this->validateSingleValue($value);
    }

    private function validateSingleValue(mixed $value): bool
    {
        $found = false;
        foreach ($this->choices as $choice) {
            if ($this->strict ? $value === $choice : $value == $choice) {
                $found = true;
                break;
            }
        }

        if (!$found) {
            $choicesString = implode(', ', array_map(
                fn($v) => is_string($v) ? "'{$v}'" : (string)$v,
                $this->choices
            ));
            $this->replaceErrorMessage("Der Wert muss einer der folgenden sein: {$choicesString}");
            return false;
        }

        return true;
    }

    private function generateDefaultMessage(): string
    {
        $choicesString = implode(', ', array_map(
            fn($v) => is_string($v) ? "'{$v}'" : (string)$v,
            $this->choices
        ));
        
        return $this->multiple 
            ? "Die Werte müssen aus folgenden Möglichkeiten gewählt werden: {$choicesString}"
            : "Der Wert muss einer der folgenden sein: {$choicesString}";
    }

    public function getErrorMessage(): string
    {
        return $this->errorMessage;
    }
} 
