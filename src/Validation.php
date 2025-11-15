<?php

namespace Javeh\ClassValidator;

use ReflectionAttribute;
use ReflectionClass;
use Javeh\ClassValidator\Contracts\ValidationAttribute;
use Javeh\ClassValidator\Contracts\Translation;
use Javeh\ClassValidator\Support\TranslationManager;

class Validation
{
    public function __construct(?Translation $translation = null)
    {
        TranslationManager::ensure($translation);
    }

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
