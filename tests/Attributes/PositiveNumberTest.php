<?php

namespace Javeh\ClassValidator\Tests\Attributes;

use Javeh\ClassValidator\Attributes\PositiveNumber;
use Javeh\ClassValidator\Tests\TestCase;

class PositiveNumberTest extends TestCase
{
    public function testAcceptsPositiveFloats(): void
    {
        $validator = new PositiveNumber();
        $this->assertTrue($validator->validate(1.5, $this->context));
    }

    public function testRejectsZero(): void
    {
        $validator = new PositiveNumber();

        $this->assertFalse($validator->validate(0, $this->context));
        $this->assertSame('The number must be positive.', $validator->getErrorMessage());
    }

    public function testRejectsNegativeNumber(): void
    {
        $validator = new PositiveNumber();

        $this->assertFalse($validator->validate(-5, $this->context));
        $this->assertSame('The number must be positive.', $validator->getErrorMessage());
    }
}
