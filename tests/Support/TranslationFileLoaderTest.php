<?php

namespace Javeh\ClassValidator\Tests\Support;

use Javeh\ClassValidator\Support\Parsers\TranslationParser;
use Javeh\ClassValidator\Support\TranslationFileLoader;
use Javeh\ClassValidator\Tests\TestCase;

class TranslationFileLoaderTest extends TestCase
{
    public function testLoadsPhpFile(): void
    {
        $path = tempnam(sys_get_temp_dir(), 'translation') . '.php';
        file_put_contents($path, "<?php return ['validation.url' => 'URL'];");

        $loader = TranslationFileLoader::withDefaultParsers(['xx' => $path]);
        $result = $loader->load();

        $this->assertSame('URL', $result['xx']['validation.url']);
        @unlink($path);
    }

    public function testCanUseCustomParser(): void
    {
        $path = tempnam(sys_get_temp_dir(), 'translation') . '.custom';
        file_put_contents($path, 'validation.url=Custom');

        $parser = new class implements TranslationParser {
            public function parse(string $path): array
            {
                [$key, $value] = explode('=', trim(file_get_contents($path)));
                return [$key => $value];
            }
            public function supports(string $extension): bool
            {
                return $extension === 'custom';
            }
        };

        $loader = new TranslationFileLoader(['xy' => $path], [$parser]);
        $result = $loader->load();

        $this->assertSame('Custom', $result['xy']['validation.url']);
        @unlink($path);
    }
}
