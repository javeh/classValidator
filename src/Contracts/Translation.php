<?php

namespace Javeh\ClassValidator\Contracts;

interface Translation
{
    public function getLocale(): string;

    public function setLocale(string $locale): void;

    public function setFallbackLocale(string $locale): void;

    public function translate(string $key, array $context = []): string;

    /**
     * Merge new messages into an existing locale.
     */
    public function extend(string $locale, array $messages): void;

    /**
     * Register a completely new locale.
     */
    public function addLocale(string $locale, array $messages): void;
}
