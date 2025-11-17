<?php

namespace Javeh\ClassValidator\Concerns;

use Javeh\ClassValidator\Support\TranslationManager;

/**
 * Helper trait to respect custom validation messages.
 */
trait HandlesValidationMessage
{
    private function initializeErrorMessage(string $defaultKey, array $context = []): void
    {
        $this->errorMessage = $this->translate($defaultKey, $context);
    }

    private function replaceErrorMessage(string $key, array $context = []): void
    {
        $this->errorMessage = $this->translate($key, $context);
    }

    private function adoptErrorMessage(string $message): void
    {
        $this->errorMessage = $message;
    }

    private function translate(string $key, array $context = []): string
    {
        return TranslationManager::get()->translate($key, $context);
    }
}
