<?php

namespace Javeh\ClassValidator\Tests\Concerns;

use Javeh\ClassValidator\Concerns\HandlesValidationMessage;
use Javeh\ClassValidator\Support\ArrayTranslation;
use Javeh\ClassValidator\Support\TranslationManager;
use Javeh\ClassValidator\Tests\TestCase;

class HandlesValidationMessageTest extends TestCase
{
    public function testTranslationsAreResolvedThroughTrait(): void
    {
        TranslationManager::set(ArrayTranslation::withDefaults('en'));
        $class = new class {
            use HandlesValidationMessage { initializeErrorMessage as public; replaceErrorMessage as public; }
            public string $errorMessage;
        };

        $class->initializeErrorMessage('validation.email');
        $this->assertSame('The value must be a valid email address.', $class->errorMessage);

        $class->replaceErrorMessage('validation.regex');
        $this->assertSame('The value must match the required pattern.', $class->errorMessage);
    }
}
