<?php

namespace Javeh\ClassValidator\Tests;

use Attribute;
use Javeh\ClassValidator\Attributes\Email;
use Javeh\ClassValidator\Contracts\ValidationAttribute;
use Javeh\ClassValidator\Support\ArrayTranslation;
use Javeh\ClassValidator\Validation;
use Javeh\ClassValidator\ValidationContext;

class ValidationTest extends TestCase
{
    public function testCollectsErrorsFromAttributes(): void
    {
        $validation = new Validation();
        $dto = new class {
            #[AlwaysFails('broken')]
            public string $foo = 'bar';
        };

        $result = $validation->validate($dto);

        $this->assertTrue($result->isInvalid());
        $this->assertSame(['foo' => ['broken']], $result->getErrors());
    }

    public function testUsesCustomTranslatorForMessages(): void
    {
        $translator = ArrayTranslation::withDefaults('en');
        $translator->extend('en', ['validation.email' => 'Custom email message']);
        $validation = new Validation($translator);

        $dto = new class {
            #[Email]
            public string $email = 'invalid';
        };

        $result = $validation->validate($dto);
        $this->assertTrue($result->isInvalid());
        $this->assertSame(['email' => ['Custom email message']], $result->getErrors());
    }

    public function testJsonSerialization(): void
    {
        $validation = new Validation();
        $dto = new class {
            #[AlwaysFails('error')]
            public string $foo = 'bar';
        };

        $result = $validation->validate($dto);

        $this->assertJsonStringEqualsJsonString(
            json_encode([
                'isValid' => false,
                'errors' => ['foo' => ['error']],
            ]),
            json_encode($result)
        );
    }
}

#[Attribute]
class AlwaysFails implements ValidationAttribute
{
    public function __construct(private string $message)
    {
    }

    public function validate(mixed $value, ValidationContext $context): bool
    {
        return false;
    }

    public function getErrorMessage(): string
    {
        return $this->message;
    }
}
