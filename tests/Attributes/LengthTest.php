<?php

namespace Javeh\ClassValidator\Tests\Attributes;

use Javeh\ClassValidator\Attributes\Length;
use Javeh\ClassValidator\Tests\TestCase;

class LengthTest extends TestCase
{
    public function testAcceptsStringWithExactLength(): void
    {
        $validator = new Length(length: 5);
        $this->assertTrue($validator->validate('hello', $this->context));
    }

    public function testRejectsArrayAboveMax(): void
    {
        $validator = new Length(max: 2);

        $this->assertFalse($validator->validate([1, 2, 3], $this->context));
        $this->assertSame('The array may not contain more than 2 items.', $validator->getErrorMessage());
    }

    public function testRejectsStringBelowMin(): void
    {
        $validator = new Length(min: 3);

        $this->assertFalse($validator->validate('hi', $this->context));
        $this->assertSame('The text must be at least 3 characters long.', $validator->getErrorMessage());
    }

    public function testRejectsUnsupportedType(): void
    {
        $validator = new Length(min: 1);

        $this->assertFalse($validator->validate(123, $this->context));
        $this->assertSame('The value must be text, an array, or a countable collection.', $validator->getErrorMessage());
    }

    public function testConstructorRequiresAtLeastOneConstraint(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('At least one of length, min, or max must be provided.');

        new Length();
    }
}
