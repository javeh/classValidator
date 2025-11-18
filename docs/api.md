# Class Validator API

This reference collects the primary entry points, contracts, and helpers of the `javeh/class-validator` package so integrators can wire up DTO validation, localisation, and custom rules without reading every source file.

## Namespace Map

| Namespace | Purpose |
| --------- | ------- |
| `Javeh\ClassValidator` | Top-level wiring, including the `Validation` orchestrator. |
| `Javeh\ClassValidator\Attributes` | Built-in attribute validators (`Text`, `Number`, `Choice`, …). |
| `Javeh\ClassValidator\Contracts` | Core contracts (`ValidationAttribute`, `Translation`). |
| `Javeh\ClassValidator\Concerns` | Helper traits (`HandlesValidationMessage`). |
| `Javeh\ClassValidator\Support` | Translator implementations, file loaders, parser plugins. |

## Validation Orchestrator

```php
use Javeh\ClassValidator\Validation;
use Javeh\ClassValidator\Support\ArrayTranslation;

$validation = new Validation(ArrayTranslation::withDefaults('en', 'de'));
$errors = $validation->validate($dto); // array<string, string[]>
```

- `__construct(?Translation $translation = null)` – optionally inject a translator; falls back to `TranslationManager::get()` which lazily bootstraps `ArrayTranslation::withDefaults('de', 'en')`.
- `validate(object $dto): array` – reflects over all properties, instantiates attached attributes that implement `ValidationAttribute`, and accumulates their error messages. Properties without attributes never appear in the result.

## Contracts

```php
interface ValidationAttribute
{
    public function validate(mixed $value): bool;
    public function getErrorMessage(): string;
}
```

Custom attributes usually `use HandlesValidationMessage` to translate default keys consistently without duplicating boilerplate.

```php
interface Translation
{
    public function getLocale(): string;
    public function setLocale(string $locale): void;
    public function setFallbackLocale(string $locale): void;
    public function translate(string $key, array $context = []): string;
    public function extend(string $locale, array $messages): void;
    public function addLocale(string $locale, array $messages): void;
}
```

## Built-in Attributes

| Attribute | Signature | Behaviour Highlights |
| --------- | --------- | -------------------- |
| `NotEmpty` | `__construct()` | Rejects `empty()` values; combine with other validators to make fields required. |
| `Text` | `__construct(?int $length = null, ?int $min = null, ?int $max = null, ?string $pattern = null)` | Accepts only strings, delegates length bounds to `Length`, optional regex via `preg_match`. |
| `Length` | `__construct(?int $length = null, ?int $min = null, ?int $max = null)` | Works for strings, arrays, and `Countable`; enforces exact/min/max counts. |
| `Number` | `__construct(?float $min = null, ?float $max = null, ?bool $integer = false, ?bool $positive = false, ?bool $negative = false, ?float $step = null)` | Guards numerical values with type checks, range, step multiples, integer- or sign-only constraints. |
| `PositiveNumber` | `__construct()` | Convenience alias ensuring numbers are strictly greater than zero. |
| `Id` | `__construct()` | Shortcut for positive integers (wraps `Number` with `integer=true, positive=true`). |
| `Range` | `__construct(int|float $min, int|float $max)` | Convenience shorthand for inclusive numeric ranges. |
| `Email` | `__construct()` | Uses `FILTER_VALIDATE_EMAIL`. |
| `Url` | `__construct()` | Uses `FILTER_VALIDATE_URL`. |
| `Choice` | `__construct(array $choices, bool $strict = true, bool $multiple = false)` | Ensures the value (or every value in an array when `multiple=true`) exists inside a whitelist. |
| `Regex` | `__construct(string $pattern)` | Validates strings with `preg_match`; constructor throws if the pattern cannot compile. |
| `Date` | `__construct(?string $format = 'Y-m-d', ?string $min = null, ?string $max = null)` | Parses using `DateTime::createFromFormat`, with optional inclusive boundaries. |
| `Instance` | `__construct(string $className)` | Checks that the value is an object of the given class/interface. |

All validators treat `null` as “not set” (auto-pass) unless the attribute’s job is to reject empties (`NotEmpty`). Refer to `docs/validation-rules.md` for the shared nullability and type policy.

## Translation System

- `ArrayTranslation::withDefaults(string $locale = 'de', string $fallbackLocale = 'en')` loads the 25 bundled PHP arrays from `resources/lang/*.php`, but the `TranslationFileLoader` also knows how to load JSON and CSV files through parser plugins residing in `Support\Parsers`.
- `TranslationManager::get()` returns the singleton translator, `::set()` overrides it globally, and `::ensure()` makes sure a translator exists before validation runs.
- Parser implementations (`PhpTranslationParser`, `JsonTranslationParser`, `CsvTranslationParser`) all implement `TranslationParser` and can be swapped or extended if you need formats such as YAML.

Example of registering a custom locale at runtime:

```php
$translator = ArrayTranslation::withDefaults('fr', 'en');
$translator->addLocale('eo', include 'resources/lang/eo.php');
$translator->extend('fr', ['validation.not_empty' => 'Ce champ est requis.']);
new Validation($translator);
```

## Developer Utilities

- Traits under `Concerns/` (currently `HandlesValidationMessage`) encapsulate repeated error-message logic; reuse them in custom attributes for consistent translator behaviour.
- PHPUnit suites reside in `tests/`. Run `vendor/bin/phpunit --coverage-text` to verify changes; coverage requires PCOV or Xdebug as configured in `phpunit.xml.dist`.
