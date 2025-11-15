<?php

namespace Javeh\ClassValidator\Concerns;

use Javeh\ClassValidator\Support\TranslationManager;

/**
 * Helper trait to respect custom validation messages.
 */
trait HandlesValidationMessage
{
    private bool $usesCustomMessage = false;

    private function initializeErrorMessage(?string $message, string $defaultKey, array $context = []): void
    {
        if ($message !== null) {
            $this->errorMessage = $message;
            $this->usesCustomMessage = true;
            return;
        }

        $this->errorMessage = $this->translate($defaultKey, $context);
        $this->usesCustomMessage = false;
    }

    private function replaceErrorMessage(string $key, array $context = []): void
    {
        if (!$this->usesCustomMessage) {
            $this->errorMessage = $this->translate($key, $context);
        }
    }

    private function adoptErrorMessage(string $message): void
    {
        if (!$this->usesCustomMessage) {
            $this->errorMessage = $message;
        }
    }

    private function translate(string $key, array $context = []): string
    {
        return TranslationManager::get()->translate($key, $context);
    }
}
