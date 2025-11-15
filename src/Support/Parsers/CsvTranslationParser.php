<?php

namespace Javeh\ClassValidator\Support\Parsers;

class CsvTranslationParser implements TranslationParser
{
    public function parse(string $path): array
    {
        $handle = fopen($path, 'r');
        if ($handle === false) {
            throw new \RuntimeException("Unable to open translation file: {$path}");
        }

        $messages = [];

        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) < 2) {
                continue;
            }

            $messages[trim($row[0])] = $row[1];
        }

        fclose($handle);

        return $messages;
    }

    public function supports(string $extension): bool
    {
        return strtolower($extension) === 'csv';
    }
}
