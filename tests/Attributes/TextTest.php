<?php

namespace Javeh\ClassValidator\Tests\Attributes;

use Javeh\ClassValidator\Attributes\Text;
use Javeh\ClassValidator\Tests\TestCase;

class TextTest extends TestCase
{
    public function testAcceptsValidText(): void
    {
        $validator = new Text(min: 3, max: 5);
        $this->assertTrue($validator->validate('four'));
    }

    public function testRejectsPatternMismatch(): void
    {
        $validator = new Text(pattern: '/^[0-9]+$/');

        $this->assertFalse($validator->validate('abc'));
        $this->assertSame('The text does not match the required pattern.', $validator->getErrorMessage());
    }

    public function testDelegatesLengthValidation(): void
    {
        $validator = new Text(min: 4);

        $this->assertFalse($validator->validate('abc'));
        $this->assertSame('The text must be at least 4 characters long.', $validator->getErrorMessage());
    }

    public function testMaxLengthConstraint(): void
    {
        $validator = new Text(max: 2);

        $this->assertFalse($validator->validate('three'));
        $this->assertSame('The text may not exceed 2 characters.', $validator->getErrorMessage());
    }
}
