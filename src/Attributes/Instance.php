<?php

namespace Idalabs\Validation\Attributes;

use Attribute;
use Idalabs\Validation\Contracts\ValidationAttribute;

#[Attribute]
class Instance implements ValidationAttribute
{
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

        $this->errorMessage = $message ?? "Der Wert muss eine Instanz von {$className} sein";
    }

    public function validate(mixed $value): bool
    {
        if (!is_object($value)) {
            $this->errorMessage = "Der Wert muss ein Objekt sein";
            return false;
        }

        if (!$value instanceof $this->className) {
            $actualClass = get_class($value);
            $this->errorMessage = "Der Wert ist eine Instanz von {$actualClass}, " .
                "muss aber eine Instanz von {$this->className} sein";
            return false;
        }

        return true;
    }

    public function getErrorMessage(): string
    {
        return $this->errorMessage;
    }
} 