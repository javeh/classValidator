<?php

namespace Javeh\ClassValidator\Tests\Attributes;

use Javeh\ClassValidator\Attributes\Email;
use Javeh\ClassValidator\Tests\TestCase;

class EmailTest extends TestCase
{
    public function testAcceptsValidEmail(): void
    {
        $validator = new Email();
        $this->assertTrue($validator->validate('user@example.com', $this->context));
    }

    public function testRejectsInvalidEmail(): void
    {
        $validator = new Email();

        $this->assertFalse($validator->validate('not-an-email', $this->context));
        $this->assertSame('The value must be a valid email address.', $validator->getErrorMessage());
    }
}
