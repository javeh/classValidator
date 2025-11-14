<?php

namespace Idalabs\Validation;

use ReflectionAttribute;
use ReflectionClass;
use ReflectionProperty;
use Idalabs\Validation\Contracts\ValidationAttribute;

class Validation
{
    public function validate(object $object): array
    {
        $errors = [];
        $reflection = new ReflectionClass($object);

        foreach ($reflection->getProperties() as $property) {
            $attributes = $property->getAttributes(ValidationAttribute::class, ReflectionAttribute::IS_INSTANCEOF);

            foreach ($attributes as $attribute) {
                /** @var ValidationAttribute $validator */
                $validator = $attribute->newInstance();
                $property->setAccessible(true);
                $value = $property->getValue($object);

                if (!$validator->validate($value)) {
                    $errors[$property->getName()][] = $validator->getErrorMessage();
                }
            }
        }

        return $errors;
    }
} 