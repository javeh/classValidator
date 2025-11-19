<?php

namespace Javeh\ClassValidator\Attributes;

use Attribute;
use Javeh\ClassValidator\Concerns\HandlesValidationMessage;
use Javeh\ClassValidator\Contracts\ValidationAttribute;
use Javeh\ClassValidator\ValidationContext;
use Javeh\ClassValidator\Support\TranslationManager;

#[Attribute]
class Instance implements ValidationAttribute
{
    use HandlesValidationMessage;

    private string $errorMessage;

    public function __construct(private readonly string $className)
    {
        if (!class_exists($className) && !interface_exists($className)) {
            throw new \InvalidArgumentException(
                TranslationManager::get()->translate('validation.config.instance.class_missing', [
                    'class' => $className,
                ])
            );
        }

        $this->initializeErrorMessage('validation.instance.required', [
            'expected' => $className,
        ]);
    }

    public function validate(mixed $value, ValidationContext $context): bool
    {
        if ($value === null) {
            return true;
        }

        if (!is_object($value)) {
            $this->replaceErrorMessage('validation.instance.object');
            return false;
        }

        if (!$value instanceof $this->className) {
            $actualClass = get_class($value);
            $this->replaceErrorMessage('validation.instance.type', [
                'actual' => $actualClass,
                'expected' => $this->className,
            ], $context);
            return false;
        }

        return true;
    }

    public function getErrorMessage(): string
    {
        return $this->errorMessage;
    }
}
