<?php

namespace Javeh\ClassValidator\Support\Parsers;

class JsonTranslationParser implements TranslationParser
{
    public function parse(string $path): array
    {
        $contents = file_get_contents($path);
        if ($contents === false) {
            throw new \RuntimeException("Unable to read translation file: {$path}");
        }

        $decoded = json_decode($contents, true, flags: JSON_THROW_ON_ERROR);

        if (!is_array($decoded)) {
            throw new \RuntimeException("JSON translation file must decode to an array: {$path}");
        }

        /** @var array<string, string> $decoded */
        return $decoded;
    }

    public function supports(string $extension): bool
    {
        return strtolower($extension) === 'json';
    }
}
