<?php

namespace Javeh\ClassValidator\Concerns;

/**
 * Helper trait to respect custom validation messages.
 */
trait HandlesValidationMessage
{
    private bool $usesCustomMessage = false;

    private function initializeErrorMessage(?string $message, string $default): void
    {
        if ($message !== null) {
            $this->errorMessage = $message;
            $this->usesCustomMessage = true;
            return;
        }

        $this->errorMessage = $default;
        $this->usesCustomMessage = false;
    }

    private function replaceErrorMessage(string $message): void
    {
        if (!$this->usesCustomMessage) {
            $this->errorMessage = $message;
        }
    }
}
