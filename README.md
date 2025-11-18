# Class Validator

PHP 8 attribute-based validator for value objects and DTOs. Annotate public properties with reusable attributes (e.g. `#[Text(min: 3)]`, `#[Number(max: 10)]`) and let the `Validation` orchestrator return a field-to-error map with localized messages.

## Installation

Requires PHP >= 8.0. Install with Composer:

```bash
composer require javeh/class-validator
```

Unit tests use PHPUnit 11; to run them locally the dev dependency is already declared in `composer.json`.

### Optional Dependencies

- **PCOV or Xdebug** if you want code coverage (`vendor/bin/phpunit --coverage-text`).

## Getting Started

```php
use Javeh\ClassValidator\Validation;
use Javeh\ClassValidator\Attributes as Assert;

class UserInput
{
    #[Assert\NotEmpty]
    #[Assert\Text(min: 3, max: 50)]
    public string $name;

    #[Assert\Email]
    public string $email;

    #[Assert\Number(min: 1, max: 5, integer: true)]
    public int $priority;
}

$input = new UserInput();
$input->name = '';
$input->email = 'nope';
$input->priority = 10;

$validation = new Validation();
$errors = $validation->validate($input);
```

`$errors` returns an array like:

```php
[
    'name' => ['The value may not be empty.'],
    'email' => ['The value must be a valid email address.'],
    'priority' => ['The number must be less than or equal to 5.'],
]
```

### Attribute Catalog

| Attribute | Description |
| --------- | ----------- |
| `NotEmpty` | Rejects empty values (falsy minus `0` constraints, uses `empty()`). |
| `Text` | Enforces string type, min/max length, exact length or regex pattern. Delegates to `Length` for long/short checks. |
| `Length` | Counts strings, arrays or countables (`length`, `min`, `max`). |
| `Number` | Numeric validation (min/max, integer-only, positive/negative flags, step multiplicity). |
| `PositiveNumber` | Alias of `Number` enforcing values greater than zero (integers or floats). |
| `Id` | Alias of `Number` with positive integer defaults (integer + positive). |
| `Range` | Numeric range check (int/float). |
| `Email`, `Url`, `Regex` | Specialized validators using PHP filters/regex. |
| `Choice` | Single or multiple selection against a list (strict or loose comparison). |
| `Instance` | Ensures the value is an object of a given class/interface. |
| `Date` | Parses `DateTime` according to a format with optional min/max boundaries. |

Add new attributes by implementing `Contracts\ValidationAttribute`.

## Localization

Messages are translated through the `Translation` contract. By default `Validation` bootstraps an `ArrayTranslation` with German output and English fallback plus 23 other European languages (`resources/lang/*.php`). To change the language:

```php
use Javeh\ClassValidator\Support\ArrayTranslation;
use Javeh\ClassValidator\Validation;

$translator = ArrayTranslation::withDefaults('en');
$translator->extend('en', ['validation.email' => 'Please enter a valid email.']);

$validation = new Validation($translator); // use translator explicitly
```

See `docs/translation.md` for custom parser/locale examples.

## Advanced Usage

- **Custom translations**: update locale files or call `$translator->extend()` / `$translator->addLocale()` to adjust copy without touching attribute constructors.
- **Optional fields**: validators treat `null` as "not set" (returns `true`) except `NotEmpty`. Combine `#[NotEmpty]` with other attributes for mandatory fields.
- **Multiple validators per property**: errors are collected in the order attributes are declared.
- **Integration tests/examples**: check `tests/Integration/ValidationIntegrationTest.php` for realistic DTO flows.

## Running Tests

```bash
vendor/bin/phpunit
vendor/bin/phpunit --coverage-text # coverage (requires pcov/xdebug)
```

CI/deployment is not bundled, but the suite is fast (dozens of tests) and covers ~84% of the codebase.

## License

MIT (see `LICENSE` if present). Pull requests welcome!
