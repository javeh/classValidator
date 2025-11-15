# Translation Guide

The validator messages are resolved through a `Translation` implementation. By default the library loads an `ArrayTranslation` instance with German output and falls back to English. Twenty-five European locales are registered out of the box via PHP array files located in `resources/lang/` (de, en, fr, es, it, pt, nl, pl, cs, hu, sv, da, no, fi, el, tr, ro, bg, hr, sr, sk, uk, ru, lt, lv, et). If a string is missing for the active locale, it automatically falls back to English.

## Usage Example

```php
use Javeh\ClassValidator\Validation;
use Javeh\ClassValidator\Support\ArrayTranslation;
use Javeh\ClassValidator\Attributes as Assert;

$translator = ArrayTranslation::withDefaults(locale: 'de');

// Optional overrides (missing keys fall back to the defaults)
$translator->extend('de', [
    'validation.text.pattern' => 'Bitte halte das gewünschte Muster ein.',
]);

// Add a completely new locale
$translator->addLocale('eo', [
    'validation.text.type' => 'La valoro devas esti teksto.',
]);

$validation = new Validation($translator);

class ExampleDto
{
    #[Assert\NotEmpty]
    #[Assert\Text(min: 4, pattern: '/^[a-z]+$/i')]
    public string $username = '';
}

$errors = $validation->validate(new ExampleDto());
```

`$errors` now contains fully translated strings based on the selected locale. If you omit the translator argument, the validation service bootstraps a default German translator.

## Adding Your Own Translator

`ArrayTranslation` implementiert das `Translation`-Interface und lädt die Sprachdateien über Parser (PHP-, JSON- und CSV-Parser sind bereits enthalten). Du kannst weitere Dateien hinzufügen, indem du sie in `resources/lang/` ablegst oder eine eigene Parser-Implementierung (`Support\Parsers\TranslationParser`) registrierst. Alternativ lässt sich natürlich eine völlig andere `Translation`-Implementierung (z. B. für Datenbanken oder APIs) schreiben. Übergib deine Instanz dem `Validation`-Konstruktor und sämtliche Validatoren greifen automatisch darauf zu; fehlende Keys fallen weiterhin auf das mitgelieferte Fallback zurück.
