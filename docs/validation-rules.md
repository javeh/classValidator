# Validation Type & Nullability Policy

Every validator follows the same rules so optional properties behave predictably:

- Validators treat `null` as “not set” and immediately return `true`. Combine the target validator with `#[NotEmpty]` (or a custom presence rule) whenever a field must be provided.
- Pure type checks guard the rest of the `validate()` logic. If a value is not of the expected type, validation fails with a deterministic error message (no implicit casting).
- Tuples/arrays/countables are only accepted by validators that explicitly state support (e.g., `Length`).
- Additional falsy values (`''`, `0`, `false`) are never auto-converted; whether they pass depends on the attribute. Text-based length checks delegate to the `Length` validator so both share identical range handling.
- Validator-Konfigurationen werden bereits im Konstruktor geprüft: Regex-Patterns müssen kompilierbar sein, Zahlen-Parameter (min/max/step) müssen logisch konsistent sein, und Date-Formate dürfen nicht leer oder ungültig sein.

Implementation hints:

1. Put `if ($value === null) { return true; }` at the top of each validator unless its sole purpose is to reject nulls (`NotEmpty`, for example).
2. Keep type guard errors short and specific, e.g. “Value must be text” or “Value must be numeric”.
3. Document deviations directly in the attribute docblock if a validator intentionally diverges (e.g., `Choice` accepting arrays in `multiple` mode).

This document should stay in sync with `src/Attributes/*`. Whenever you add a validator or change its expectations, update both the implementation and this policy.***
