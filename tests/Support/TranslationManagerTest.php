<?php

namespace Javeh\ClassValidator\Tests\Support;

use Javeh\ClassValidator\Contracts\Translation;
use Javeh\ClassValidator\Support\ArrayTranslation;
use Javeh\ClassValidator\Support\TranslationManager;
use Javeh\ClassValidator\Tests\TestCase;

class TranslationManagerTest extends TestCase
{
    public function testReturnsDefaultInstanceWhenNoneSet(): void
    {
        TranslationManager::set(ArrayTranslation::withDefaults('en'));
        $this->assertInstanceOf(ArrayTranslation::class, TranslationManager::get());
    }

    public function testCustomTranslationCanBeInjected(): void
    {
        $custom = new class implements Translation {
            public function getLocale(): string { return 'custom'; }
            public function setLocale(string $locale): void {}
            public function setFallbackLocale(string $locale): void {}
            public function translate(string $key, array $context = []): string { return 'custom:' . $key; }
            public function extend(string $locale, array $messages): void {}
            public function addLocale(string $locale, array $messages): void {}
        };

        TranslationManager::set($custom);
        $this->assertSame('custom:validation.email', TranslationManager::get()->translate('validation.email'));
    }
}
