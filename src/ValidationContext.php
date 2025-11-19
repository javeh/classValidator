<?php

namespace Javeh\ClassValidator;

use Javeh\ClassValidator\Contracts\Translation;

readonly class ValidationContext
{
    public function __construct(
        public Translation $translator
    ) {
    }

    public function translate(string $key, array $replace = []): string
    {
        return $this->translator->translate($key, $replace);
    }
}
