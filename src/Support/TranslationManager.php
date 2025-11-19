<?php

namespace Javeh\ClassValidator\Support;

use Javeh\ClassValidator\Contracts\Translation;

/**
 * @deprecated This class introduces global state and will be removed in v1.0. Use ValidationContext instead.
 */
class TranslationManager
{
    private static ?Translation $translator = null;

    public static function get(): Translation
    {
        if (!self::$translator) {
            self::$translator = ArrayTranslation::withDefaults();
        }

        return self::$translator;
    }

    public static function set(Translation $translation): void
    {
        self::$translator = $translation;
    }

    public static function ensure(?Translation $translation = null): Translation
    {
        if ($translation) {
            self::$translator = $translation;
        }

        return self::get();
    }
}
