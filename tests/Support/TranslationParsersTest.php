<?php

namespace Javeh\ClassValidator\Tests\Support;

use Javeh\ClassValidator\Support\Parsers\CsvTranslationParser;
use Javeh\ClassValidator\Support\Parsers\JsonTranslationParser;
use Javeh\ClassValidator\Support\Parsers\PhpTranslationParser;
use Javeh\ClassValidator\Tests\TestCase;

class TranslationParsersTest extends TestCase
{
    public function testPhpParserReadsArray(): void
    {
        $file = tempnam(sys_get_temp_dir(), 'php-trans') . '.php';
        file_put_contents($file, "<?php return ['validation.regex' => 'Regex'];");

        $parser = new PhpTranslationParser();
        $data = $parser->parse($file);

        $this->assertSame('Regex', $data['validation.regex']);
        $this->assertTrue($parser->supports('php'));
        @unlink($file);
    }

    public function testJsonParserReadsFile(): void
    {
        $file = tempnam(sys_get_temp_dir(), 'json-trans') . '.json';
        file_put_contents($file, json_encode(['validation.range' => 'Range']));

        $parser = new JsonTranslationParser();
        $data = $parser->parse($file);

        $this->assertSame('Range', $data['validation.range']);
        $this->assertTrue($parser->supports('json'));
        @unlink($file);
    }

    public function testCsvParserReadsRows(): void
    {
        $file = tempnam(sys_get_temp_dir(), 'csv-trans') . '.csv';
        file_put_contents($file, "\"validation.url\",\"URL\"\n");

        $parser = new CsvTranslationParser();
        $data = $parser->parse($file);

        $this->assertSame('URL', $data['validation.url']);
        $this->assertTrue($parser->supports('csv'));
        @unlink($file);
    }
}
