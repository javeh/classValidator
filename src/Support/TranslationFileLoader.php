<?php

namespace Javeh\ClassValidator\Support;

use Javeh\ClassValidator\Support\Parsers\CsvTranslationParser;
use Javeh\ClassValidator\Support\Parsers\JsonTranslationParser;
use Javeh\ClassValidator\Support\Parsers\PhpTranslationParser;
use Javeh\ClassValidator\Support\Parsers\TranslationParser;

class TranslationFileLoader
{
    /**
     * @param array<string, string> $localeFiles
     * @param TranslationParser[] $parsers
     */
    public function __construct(
        private readonly array $localeFiles,
        private readonly array $parsers
    ) {
    }

    public static function withDefaultParsers(array $localeFiles): self
    {
        return new self($localeFiles, [
            new PhpTranslationParser(),
            new JsonTranslationParser(),
            new CsvTranslationParser(),
        ]);
    }

    /**
     * @return array<string, array<string, string>>
     */
    public function load(): array
    {
        $messages = [];

        foreach ($this->localeFiles as $locale => $path) {
            if (!is_file($path)) {
                continue;
            }

            $extension = strtolower((string)pathinfo($path, PATHINFO_EXTENSION));
            $parser = $this->findParser($extension);

            if (!$parser) {
                continue;
            }

            $messages[$locale] = $parser->parse($path);
        }

        return $messages;
    }

    private function findParser(string $extension): ?TranslationParser
    {
        foreach ($this->parsers as $parser) {
            if ($parser->supports($extension)) {
                return $parser;
            }
        }

        return null;
    }
}
