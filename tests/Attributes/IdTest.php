<?php

namespace Javeh\ClassValidator\Tests\Attributes;

use Javeh\ClassValidator\Attributes\Id;
use Javeh\ClassValidator\Tests\TestCase;

class IdTest extends TestCase
{
    public function testAcceptsPositiveInteger(): void
    {
        $validator = new Id();
        $this->assertTrue($validator->validate(42));
    }

    public function testRejectsZeroOrNegative(): void
    {
        $validator = new Id();

        $this->assertFalse($validator->validate(0));
        $this->assertSame('The number must be positive.', $validator->getErrorMessage());
    }

    public function testRejectsNonInteger(): void
    {
        $validator = new Id();

        $this->assertFalse($validator->validate(3.14));
        $this->assertSame('The value must be an integer.', $validator->getErrorMessage());
    }
}
