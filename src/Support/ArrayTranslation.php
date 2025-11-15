<?php

namespace Javeh\ClassValidator\Support;

use Javeh\ClassValidator\Contracts\Translation;

class ArrayTranslation implements Translation
{
    private array $messages;

    public function __construct(
        array $messages,
        private string $locale = 'de',
        private string $fallbackLocale = 'en'
    ) {
        $this->messages = $messages;
    }

    public static function withDefaults(string $locale = 'de', string $fallbackLocale = 'en'): self
    {
        return new self(self::defaultMessages(), $locale, $fallbackLocale);
    }

    public function getLocale(): string
    {
        return $this->locale;
    }

    public function setLocale(string $locale): void
    {
        $this->locale = $locale;
    }

    public function setFallbackLocale(string $locale): void
    {
        $this->fallbackLocale = $locale;
    }

    public function translate(string $key, array $context = []): string
    {
        $localeMessages = $this->messages[$this->locale] ?? [];
        $fallbackMessages = $this->messages[$this->fallbackLocale] ?? [];

        $message = $localeMessages[$key] ?? $fallbackMessages[$key] ?? $key;

        return $this->formatMessage($message, $context);
    }

    public function extend(string $locale, array $messages): void
    {
        if (!isset($this->messages[$locale])) {
            $this->messages[$locale] = [];
        }

        $this->messages[$locale] = array_merge($this->messages[$locale], $messages);
    }

    public function addLocale(string $locale, array $messages): void
    {
        $this->messages[$locale] = $messages;
    }

    private function formatMessage(string $message, array $context): string
    {
        foreach ($context as $placeholder => $value) {
            $message = str_replace(':' . $placeholder, (string)$value, $message);
        }

        return $message;
    }

    private static function defaultMessages(): array
    {
        $basePath = dirname(__DIR__, 2) . '/resources/lang';

        $files = [
            'en' => $basePath . '/en.php',
            'de' => $basePath . '/de.php',
            'fr' => $basePath . '/fr.php',
            'es' => $basePath . '/es.php',
            'it' => $basePath . '/it.php',
            'pt' => $basePath . '/pt.php',
            'nl' => $basePath . '/nl.php',
            'pl' => $basePath . '/pl.php',
            'cs' => $basePath . '/cs.php',
            'hu' => $basePath . '/hu.php',
            'sv' => $basePath . '/sv.php',
            'da' => $basePath . '/da.php',
            'no' => $basePath . '/no.php',
            'fi' => $basePath . '/fi.php',
            'el' => $basePath . '/el.php',
            'tr' => $basePath . '/tr.php',
            'ro' => $basePath . '/ro.php',
            'bg' => $basePath . '/bg.php',
            'hr' => $basePath . '/hr.php',
            'sr' => $basePath . '/sr.php',
            'sk' => $basePath . '/sk.php',
            'uk' => $basePath . '/uk.php',
            'ru' => $basePath . '/ru.php',
            'lt' => $basePath . '/lt.php',
            'lv' => $basePath . '/lv.php',
            'et' => $basePath . '/et.php',
        ];

        $loader = TranslationFileLoader::withDefaultParsers($files);
        $messages = $loader->load();

        return self::attachFallbackLocales($messages);
    }

    private static function attachFallbackLocales(array $messages): array
    {
        $fallbackLocales = [
            'fr', 'es', 'it', 'pt', 'nl', 'pl', 'cs', 'hu', 'sv', 'da', 'no', 'fi',
            'el', 'tr', 'ro', 'bg', 'hr', 'sr', 'sk', 'uk', 'ru', 'lt', 'lv', 'et',
        ];

        $default = $messages['en'] ?? [];

        foreach ($fallbackLocales as $locale) {
            if (!isset($messages[$locale])) {
                $messages[$locale] = $default;
            }
        }

        return $messages;
    }
}
