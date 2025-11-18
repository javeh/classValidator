<?php

namespace Javeh\ClassValidator\Attributes;

use Attribute;
use Javeh\ClassValidator\Concerns\HandlesValidationMessage;
use Javeh\ClassValidator\Contracts\ValidationAttribute;
use Javeh\ClassValidator\Support\TranslationManager;

#[Attribute]
class Regex implements ValidationAttribute
{
    use HandlesValidationMessage;

    private string $errorMessage;

    public function __construct(private readonly string $pattern)
    {
        $this->assertPatternIsValid($pattern);
        $this->initializeErrorMessage('validation.regex');
    }

    public function validate(mixed $value): bool
    {
        if ($value === null) {
            return true;
        }

        if (is_string($value) && preg_match($this->pattern, $value)) {
            return true;
        }

        $this->replaceErrorMessage('validation.regex');
        return false;
    }

    public function getErrorMessage(): string
    {
        return $this->errorMessage;
    }

    private function assertPatternIsValid(string $pattern): void
    {
        set_error_handler(static function () {
            /* swallow warnings; handled via preg_last_error */
        });
        preg_match($pattern, '');
        restore_error_handler();

        if (preg_last_error() !== PREG_NO_ERROR) {
            throw new \InvalidArgumentException(
                TranslationManager::get()->translate('validation.config.regex.pattern', [
                    'pattern' => $pattern,
                ])
            );
        }
    }
}
