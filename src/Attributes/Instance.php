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

        $this->initializeErrorMessage($message, "Der Wert muss eine Instanz von {$className} sein");
    }

    public function validate(mixed $value): bool
    {
        if ($value === null) {
            return true;
        }

        if (!is_object($value)) {
            $this->replaceErrorMessage("Der Wert muss ein Objekt sein");
            return false;
        }

        if (!$value instanceof $this->className) {
            $actualClass = get_class($value);
            $this->replaceErrorMessage(
                "Der Wert ist eine Instanz von {$actualClass}, muss aber eine Instanz von {$this->className} sein"
            );
            return false;
        }

        return true;
    }

    public function getErrorMessage(): string
    {
        return $this->errorMessage;
    }
} 
