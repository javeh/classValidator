<?php

namespace Javeh\ClassValidator\Tests\Attributes;

use Javeh\ClassValidator\Attributes\Range;
use Javeh\ClassValidator\Tests\TestCase;

class RangeTest extends TestCase
{
    public function testAcceptsValueInsideRange(): void
    {
        $validator = new Range(1, 5);
        $this->assertTrue($validator->validate(3));
    }

    public function testRejectsValueOutsideRange(): void
    {
        $validator = new Range(1, 5);

        $this->assertFalse($validator->validate(8));
        $this->assertSame('The value must be between 1 and 5.', $validator->getErrorMessage());
    }

    public function testTreatsNullAsValid(): void
    {
        $validator = new Range(0, 100);
        $this->assertTrue($validator->validate(null));
    }

    public function testConstructorRejectsMaxBelowMin(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The maximum value must be greater than or equal to the minimum value.');

        new Range(10, 5);
    }
}
