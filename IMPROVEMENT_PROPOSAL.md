# Architectural Improvement Proposal

Based on the analysis of the `javeh/class-validator` library, here are several recommendations to improve the architecture, testability, and maintainability of the project.

## 1. Remove Global State from `TranslationManager` (Completed)

**Status:** ✅ Completed in v0.3.0

**Changes Implemented:**
- Introduced `ValidationContext` to hold the `Translation` instance.
- Updated `ValidationAttribute` interface to accept `ValidationContext` in `validate`.
- Refactored all attributes to use the context for translation.
- Deprecated `TranslationManager` (to be removed in v1.0).
- Updated `Validation` class to inject the context.

**Previous State:**
The `TranslationManager` relied on a static `$translator` property and static methods (`get`, `set`, `ensure`). This introduced global state, making the library harder to test.

**Recommendation (Implemented):**
- Refactor `TranslationManager` to be a regular instance class or remove it entirely in favor of passing the `Translation` contract directly.
- The `Validation` class already accepts `?Translation $translation` in its constructor. It should store this instance and pass it to validators, rather than relying on a static manager.
- **Solution:** Introduced a `ValidationContext` that is passed to the `validate` method of the attribute.

## 2. Decouple Attributes from Validation Logic

**Current State:**
Attributes like `Email` and `Choice` implement `ValidationAttribute` and contain both configuration (properties) and logic (`validate` method).
This follows the "Active Record" pattern for attributes. While simple, it limits dependency injection (e.g., if a validator needs a database connection or an external service).

**Recommendation:**
Consider separating the Attribute (DTO) from the Validator (Service).
- **Attribute:** `#[Email]` (holds configuration)
- **Validator:** `EmailValidator` (contains logic)
- **Registry:** A way to map Attributes to Validators.

*Note:* This is a significant refactor. For a lightweight library, the current approach is acceptable, but the `Translation` dependency issue (Point 1) needs solving. A middle ground is to pass a `ValidationContext` to the `validate` method:

```php
interface ValidationAttribute
{
    public function validate(mixed $value, ValidationContext $context): bool;
}
```

The `ValidationContext` would hold the `Translation` instance and any other shared resources.

## 3. Introduce `ValidationResult` Object (Completed)

**Status:** ✅ Completed in v0.3.0

**Changes Implemented:**
- Created `ValidationResult` class.
- Updated `Validation::validate` to return `ValidationResult` instead of `array`.
- Updated tests to use `ValidationResult` methods.

**Previous State:**
`Validation::validate` returned a raw `array` of errors.

**Recommendation (Implemented):**
Return a `ValidationResult` object.
- **Benefits:**
    - Type safety.
    - Helper methods: `$result->isValid()`, `$result->getErrors()`, `$result->firstError()`.
    - JSON serialization support.
    - Future-proofing (can add metadata later).

## 4. Enhance `Validation` Class

**Current State:**
The `Validation` class creates `ReflectionClass` internally.

**Recommendation:**
- Allow passing a `ReflectionClass` or `ReflectionProperty` to the validator to avoid re-reflecting if cached.
- Add a static facade or helper if ease of use is desired, but keep the core class pure.

## 5. Strict Typing & Modern PHP Features

**Current State:**
The code uses PHP 8.2 features.

**Recommendation:**
- Ensure `readonly` classes are used where appropriate (e.g. Attributes could be readonly).
- Use `true` / `false` standalone types where applicable.

## Summary of Immediate Actions

1.  **Refactor `TranslationManager`**: Eliminate static state. Pass `Translation` instance through a context object to attributes. (✅ Done)
2.  **Create `ValidationResult`**: Replace array return type. (✅ Done)
3.  **Refactor `Validation::validate`**: To use the new Context and Result patterns. (✅ Done)

## Example Refactoring (Sketch)

```php
// src/ValidationContext.php
class ValidationContext {
    public function __construct(public readonly Translation $translator) {}
}

// src/Contracts/ValidationAttribute.php
interface ValidationAttribute {
    public function validate(mixed $value, ValidationContext $context): bool;
}

// src/Validation.php
class Validation {
    public function __construct(private Translation $translator) {}

    public function validate(object $object): ValidationResult {
        $context = new ValidationContext($this->translator);
        // ... loop ...
        $validator->validate($value, $context);
        // ...
    }
}
```
