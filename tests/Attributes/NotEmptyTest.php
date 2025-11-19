<?php

namespace Javeh\ClassValidator\Tests\Attributes;

use Javeh\ClassValidator\Attributes\NotEmpty;
use Javeh\ClassValidator\Tests\TestCase;

class NotEmptyTest extends TestCase
{
    public function testAcceptsNonEmptyValue(): void
    {
        $validator = new NotEmpty();
        $this->assertTrue($validator->validate('value', $this->context));
    }

    public function testRejectsEmptyValue(): void
    {
        $validator = new NotEmpty();

        $this->assertFalse($validator->validate('', $this->context));
        $this->assertSame('The value may not be empty.', $validator->getErrorMessage());
    }
}
