<?php

namespace Javeh\ClassValidator\Tests\Concerns;

use Javeh\ClassValidator\Concerns\HandlesValidationMessage;
use Javeh\ClassValidator\Support\ArrayTranslation;
use Javeh\ClassValidator\Support\TranslationManager;
use Javeh\ClassValidator\Tests\TestCase;

class HandlesValidationMessageTest extends TestCase
{
    public function testCustomMessageIsRespected(): void
    {
        $class = new class {
            use HandlesValidationMessage { initializeErrorMessage as public; replaceErrorMessage as public; }
            public string $errorMessage;
        };

        $class->initializeErrorMessage('Custom message', 'validation.not_empty');
        $class->replaceErrorMessage('validation.email');

        $this->assertSame('Custom message', $class->errorMessage);
    }

    public function testTranslationUsedWhenNoCustomMessage(): void
    {
        TranslationManager::set(ArrayTranslation::withDefaults('en'));
        $class = new class {
            use HandlesValidationMessage { initializeErrorMessage as public; replaceErrorMessage as public; }
            public string $errorMessage;
        };

        $class->initializeErrorMessage(null, 'validation.email');
        $this->assertSame('The value must be a valid email address.', $class->errorMessage);

        $class->replaceErrorMessage('validation.regex');
        $this->assertSame('The value must match the required pattern.', $class->errorMessage);
    }
}
