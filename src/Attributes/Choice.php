<?php

namespace Javeh\ClassValidator\Attributes;

use Attribute;
use Javeh\ClassValidator\Concerns\HandlesValidationMessage;
use Javeh\ClassValidator\Contracts\ValidationAttribute;
use Javeh\ClassValidator\Support\TranslationManager;
use Javeh\ClassValidator\ValidationContext;

#[Attribute]
class Choice implements ValidationAttribute
{
    use HandlesValidationMessage;

    private string $errorMessage;
    private array $choices;

    public function __construct(
        array $choices,
        private readonly bool $strict = true,
        private readonly bool $multiple = false
    ) {
        if (empty($choices)) {
            throw new \InvalidArgumentException(
                TranslationManager::get()->translate('validation.config.choice.empty')
            );
        }

        $this->choices = array_values($choices);
        $this->initializeErrorMessage(
            $this->multiple ? 'validation.choice.multiple' : 'validation.choice.single',
            ['choices' => $this->formatChoices()]
        );
    }

    public function validate(mixed $value, ValidationContext $context): bool
    {
        if ($value === null) {
            return true;
        }

        if ($this->multiple) {
            if (!is_array($value)) {
                $this->replaceErrorMessage('validation.choice.array', [], $context);
                return false;
            }

            foreach ($value as $item) {
                if (!$this->validateSingleValue($item, $context)) {
                    return false;
                }
            }
            return true;
        }

        return $this->validateSingleValue($value, $context);
    }

    private function validateSingleValue(mixed $value, ValidationContext $context): bool
    {
        foreach ($this->choices as $choice) {
            if ($this->strict ? $value === $choice : $value == $choice) {
                return true;
            }
        }

        $this->replaceErrorMessage(
            $this->multiple ? 'validation.choice.multiple' : 'validation.choice.single',
            ['choices' => $this->formatChoices()],
            $context
        );
        return false;
    }

    private function formatChoices(): string
    {
        return implode(', ', array_map(
            fn($v) => is_string($v) ? "'{$v}'" : (string) $v,
            $this->choices
        ));
    }

    public function getErrorMessage(): string
    {
        return $this->errorMessage;
    }
}
