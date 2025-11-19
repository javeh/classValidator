<?php

namespace Javeh\ClassValidator\Concerns;

use Javeh\ClassValidator\Support\TranslationManager;
use Javeh\ClassValidator\ValidationContext;

/**
 * Helper trait to respect custom validation messages.
 */
trait HandlesValidationMessage
{
    private function initializeErrorMessage(string $defaultKey, array $context = []): void
    {
        $this->errorMessage = $this->translate($defaultKey, $context);
    }

    private function replaceErrorMessage(string $key, array $replace = [], ?ValidationContext $context = null): void
    {
        $this->errorMessage = $this->translate($key, $replace, $context);
    }

    private function adoptErrorMessage(string $message): void
    {
        $this->errorMessage = $message;
    }

    private function translate(string $key, array $replace = [], ?ValidationContext $context = null): string
    {
        if ($context) {
            return $context->translate($key, $replace);
        }
        return TranslationManager::get()->translate($key, $replace);
    }
}
