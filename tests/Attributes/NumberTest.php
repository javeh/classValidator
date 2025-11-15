<?php

namespace Javeh\ClassValidator\Tests\Attributes;

use Javeh\ClassValidator\Attributes\Number;
use Javeh\ClassValidator\Tests\TestCase;

class NumberTest extends TestCase
{
    public function testAcceptsNumberWithinRange(): void
    {
        $validator = new Number(min: 1, max: 10);
        $this->assertTrue($validator->validate(5));
    }

    public function testRejectsNonNumericValues(): void
    {
        $validator = new Number();

        $this->assertFalse($validator->validate('abc'));
        $this->assertSame('The value must be a number.', $validator->getErrorMessage());
    }

    public function testRejectsStepMismatch(): void
    {
        $validator = new Number(step: 2);

        $this->assertFalse($validator->validate(3));
        $this->assertSame('The number must be a multiple of 2.', $validator->getErrorMessage());
    }

    public function testRejectsBelowMinAndAboveMax(): void
    {
        $validator = new Number(min: 10);
        $this->assertFalse($validator->validate(5));
        $this->assertSame('The number must be greater than or equal to 10.', $validator->getErrorMessage());

        $validator = new Number(max: 3);
        $this->assertFalse($validator->validate(4));
        $this->assertSame('The number must be less than or equal to 3.', $validator->getErrorMessage());
    }

    public function testIntegerFlagRejectsFloats(): void
    {
        $validator = new Number(integer: true);
        $this->assertFalse($validator->validate(1.5));
        $this->assertSame('The value must be an integer.', $validator->getErrorMessage());
    }
}
