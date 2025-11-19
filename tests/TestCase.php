<?php

namespace Javeh\ClassValidator\Tests;

use Javeh\ClassValidator\Support\ArrayTranslation;
use Javeh\ClassValidator\Support\TranslationManager;
use PHPUnit\Framework\TestCase as BaseTestCase;

use Javeh\ClassValidator\ValidationContext;

abstract class TestCase extends BaseTestCase
{
    protected ValidationContext $context;

    protected function setUp(): void
    {
        parent::setUp();
        TranslationManager::set(ArrayTranslation::withDefaults('en'));
        $this->context = new ValidationContext(TranslationManager::get());
    }
}
