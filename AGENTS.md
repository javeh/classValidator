# Repository Guidelines

## Project Structure & Module Organization
Source lives under `src/`, PSR-4 mapped to `Javeh\ClassValidator\`. Attribute validators sit in `src/Attributes/`, shared contracts in `src/Contracts/`, and the orchestrator entry point is `src/Validation.php`. Keep any reusable helpers inside dedicated subfolders (e.g., `src/Support/`) to avoid crowding the root namespace. Composer-managed code lands in `vendor/` and stays untracked. Place PHPUnit specs inside `tests/`, mirroring the `src/` tree (`tests/Attributes/RequiredAttributeTest.php` for `src/Attributes/Required.php`).

## Build, Test, and Development Commands
- `composer install` — install dependencies and generate the autoloader.
- `composer dump-autoload -o` — refresh the optimized autoload map after namespace or file changes.
- `vendor/bin/phpunit` — run the full test suite; append `--filter ClassName` to target one case.
Execute commands from the repository root to ensure Composer picks up `composer.json`.

## Coding Style & Naming Conventions
Follow PSR-12 with four-space indentation, LF line endings, and strict types declared at the top of every PHP file. Class, attribute, and contract names use StudlyCase (`EmailAttribute`, `ValidationAttribute`), while properties and methods adopt camelCase. Keep attribute messages short and localized; collocate language-specific text near the attribute so translations stay manageable. Run a formatter such as `phpcbf` locally if available, and avoid committing IDE metadata beyond `.idea` workspace files already ignored.

## Testing Guidelines
Write PHPUnit test cases in `tests/`, arranging directories to mirror the production namespace. Name methods descriptively (`testItRejectsEmptyValues`) and favor data providers for matrix inputs. When fixing a bug or adding a new attribute, craft a regression test before modifying implementation. Aim for high coverage on `Validation` and complex attribute classes because they encapsulate most business rules. Use `vendor/bin/phpunit --coverage-text` to spot gaps before opening a pull request.

## Commit & Pull Request Guidelines
Commits follow the concise, present-tense style already in history (e.g., `Add .gitignore`, `Second commit`). Group related changes together and include references to GitHub issues or Packagist tickets when relevant. Pull requests should summarize the motivation, outline the solution, list manual or automated test evidence, and flag any breaking changes or new configuration steps. Add screenshots or payload samples only when API behavior or validation messages change so reviewers can validate the UX impact quickly.
