<?php

namespace Javeh\ClassValidator\Tests\Integration;

use Javeh\ClassValidator\Attributes as Assert;
use Javeh\ClassValidator\Support\ArrayTranslation;
use Javeh\ClassValidator\Support\TranslationManager;
use Javeh\ClassValidator\Tests\TestCase;
use Javeh\ClassValidator\Validation;

class ValidationIntegrationTest extends TestCase
{
    public function testValidDtoProducesNoErrors(): void
    {
        $dto = new class {
            #[Assert\NotEmpty]
            #[Assert\Text(min: 3, max: 12)]
            public string $name = 'Validator';

            #[Assert\Email]
            public string $email = 'user@example.com';

            #[Assert\Number(min: 1, max: 10, integer: true)]
            public int $level = 5;
        };

        $validation = new Validation();
        $this->assertSame([], $validation->validate($dto));
    }

    public function testInvalidDtoCollectsErrorsWithTranslations(): void
    {
        $translator = ArrayTranslation::withDefaults('de');
        $validation = new Validation($translator);

        $dto = new class {
            #[Assert\NotEmpty]
            #[Assert\Text(min: 5, pattern: '/^[a-z]+$/i')]
            public string $username = 'Hi!';

            #[Assert\Choice(['foo', 'bar'], multiple: true)]
            public array $roles = ['admin'];

            #[Assert\Range(10, 20)]
            public int $score = 5;
        };

        $errors = $validation->validate($dto);

        $this->assertSame(
            [
                'username' => [
                    'Der Text muss mindestens 5 Zeichen lang sein.',
                ],
                'roles' => [
                    "Die Werte müssen aus folgenden Möglichkeiten gewählt werden: 'foo', 'bar'.",
                ],
                'score' => [
                    'Der Wert muss zwischen 10 und 20 liegen.',
                ],
            ],
            $errors
        );
    }

    public function testTranslatorSwitchingPerValidationRun(): void
    {
        $dto = new class {
            #[Assert\Email]
            public string $email = 'invalid';
        };

        TranslationManager::set(ArrayTranslation::withDefaults('en'));
        $english = new Validation();
        $this->assertSame('The value must be a valid email address.', $english->validate($dto)['email'][0]);

        TranslationManager::set(ArrayTranslation::withDefaults('de'));
        $german = new Validation();
        $this->assertSame('Der Wert muss eine gültige E-Mail-Adresse sein.', $german->validate($dto)['email'][0]);
    }

    public function testOptionalFieldsSkipValidationWhenNull(): void
    {
        $validation = new Validation();

        $dto = new class {
            #[Assert\Text(min: 3)]
            #[Assert\Regex('/^[a-z]+$/i')]
            public ?string $nickname = null;

            #[Assert\NotEmpty]
            public ?string $mandatory = null;
        };

        $errors = $validation->validate($dto);
        $this->assertSame([
            'mandatory' => ['The value may not be empty.'],
        ], $errors);
    }

    public function testMultipleAttributeTypesProduceAggregatedErrors(): void
    {
        TranslationManager::set(ArrayTranslation::withDefaults('en'));
        $validation = new Validation();

        $dto = new class {
            #[Assert\NotEmpty]
            #[Assert\Text(min: 4, max: 10)]
            public string $title = '';

            #[Assert\Choice(['editor', 'viewer'], multiple: true)]
            public array $roles = ['admin'];

            #[Assert\Number(min: 1, max: 5, integer: true)]
            public float $level = 7.5;
        };

        $errors = $validation->validate($dto);
        $this->assertSame([
            'title' => [
                'The value may not be empty.',
                'The text must be at least 4 characters long.',
            ],
            'roles' => [
                "Values must be chosen from 'editor', 'viewer'.",
            ],
            'level' => [
                'The value must be an integer.',
            ],
        ], $errors);
    }
}
