<?php

namespace Javeh\ClassValidator\Attributes;

use Attribute;
use Javeh\ClassValidator\Concerns\HandlesValidationMessage;
use Javeh\ClassValidator\Contracts\ValidationAttribute;

#[Attribute]
class Instance implements ValidationAttribute
{
    use HandlesValidationMessage;

    private string $errorMessage;

    public function __construct(
        private readonly string $className,
        ?string $message = null
    ) {
        if (!class_exists($className) && !interface_exists($className)) {
            throw new \InvalidArgumentException(
                "Die Klasse oder Interface '{$className}' existiert nicht"
            );
        }

        $this->initializeErrorMessage($message, 'validation.instance.required', [
            'expected' => $className,
        ]);
    }

    public function validate(mixed $value): bool
    {
        if ($value === null) {
            return true;
        }

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
            ]);
            return false;
        }

        return true;
    }

    public function getErrorMessage(): string
    {
        return $this->errorMessage;
    }
} 
