<?php

namespace Javeh\ClassValidator\Support\Parsers;

class PhpTranslationParser implements TranslationParser
{
    public function parse(string $path): array
    {
        /** @var mixed $messages */
        $messages = include $path;

        if (!is_array($messages)) {
            throw new \RuntimeException("PHP translation file must return an array: {$path}");
        }

        return $messages;
    }

    public function supports(string $extension): bool
    {
        return strtolower($extension) === 'php';
    }
}
