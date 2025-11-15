<?php

namespace Javeh\ClassValidator\Support\Parsers;

interface TranslationParser
{
    /**
     * @return array<string, string>
     */
    public function parse(string $path): array;

    public function supports(string $extension): bool;
}
