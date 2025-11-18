<?php

namespace Javeh\ClassValidator\Tests\Support;

use Javeh\ClassValidator\Tests\TestCase;

class TranslationIntegrityTest extends TestCase
{
    public function testConfigKeysExistForAllLocales(): void
    {
        $keys = [
            'validation.config.choice.empty',
            'validation.config.length.constraint',
            'validation.config.number.sign_conflict',
            'validation.config.number.step_positive',
            'validation.config.number.bounds',
            'validation.config.range.bounds',
            'validation.config.instance.class_missing',
            'validation.config.date.bounds',
            'validation.config.date.format_empty',
            'validation.config.date.format_invalid',
            'validation.config.date.boundary',
            'validation.config.date.min_label',
            'validation.config.date.max_label',
            'validation.config.regex.pattern',
        ];

        $basePath = dirname(__DIR__, 2) . '/resources/lang';
        $missing = [];

        foreach (glob($basePath . '/*.php') as $file) {
            $messages = include $file;
            $locale = basename($file);
            $missingKeys = array_filter($keys, static fn ($key) => !array_key_exists($key, $messages));

            if (!empty($missingKeys)) {
                $missing[$locale] = $missingKeys;
            }
        }

        $this->assertSame([], $missing, 'Some locales are missing translation keys for configuration errors.');
    }
}
