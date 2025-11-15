<?php

namespace Javeh\ClassValidator\Tests;

use Javeh\ClassValidator\Support\ArrayTranslation;
use Javeh\ClassValidator\Support\TranslationManager;
use PHPUnit\Framework\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        TranslationManager::set(ArrayTranslation::withDefaults('en'));
    }
}
