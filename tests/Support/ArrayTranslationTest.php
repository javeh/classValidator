<?php

namespace Javeh\ClassValidator\Tests\Support;

use Javeh\ClassValidator\Support\ArrayTranslation;
use Javeh\ClassValidator\Tests\TestCase;

class ArrayTranslationTest extends TestCase
{
    public function testTranslatesUsingDefaultLocale(): void
    {
        $translator = ArrayTranslation::withDefaults('en');
        $this->assertSame(
            'The value must be a valid email address.',
            $translator->translate('validation.email')
        );
    }

    public function testExtendOverridesMessage(): void
    {
        $translator = ArrayTranslation::withDefaults('de');
        $translator->extend('de', ['validation.email' => 'Eigene Nachricht']);

        $this->assertSame('Eigene Nachricht', $translator->translate('validation.email'));
    }

    public function testAddLocaleAllowsCustomLanguage(): void
    {
        $translator = ArrayTranslation::withDefaults('en');
        $translator->addLocale('eo', ['validation.email' => 'Tio devas esti retadreso.']);
        $translator->setLocale('eo');

        $this->assertSame('Tio devas esti retadreso.', $translator->translate('validation.email'));
    }
}
