<?php

namespace Javeh\ClassValidator\Tests\Attributes;

use Javeh\ClassValidator\Attributes\Choice;
use Javeh\ClassValidator\Tests\TestCase;

class ChoiceTest extends TestCase
{
    public function testAcceptsStrictSingleValue(): void
    {
        $validator = new Choice(['A', 'B']);

        $this->assertTrue($validator->validate('A'));
        $this->assertTrue($validator->validate(null), 'null should be treated as not set');
    }

    public function testRejectsValueOutsideChoices(): void
    {
        $validator = new Choice(['foo', 'bar']);

        $this->assertFalse($validator->validate('baz'));
        $this->assertSame("The value must be one of 'foo', 'bar'.", $validator->getErrorMessage());
    }

    public function testMultipleModeRequiresArray(): void
    {
        $validator = new Choice(['x', 'y'], multiple: true);

        $this->assertFalse($validator->validate('x'));
        $this->assertSame('The value must be an array.', $validator->getErrorMessage());
    }

    public function testMultipleModeValidatesEachItem(): void
    {
        $validator = new Choice(['apple', 'banana'], multiple: true, strict: false);

        $this->assertTrue($validator->validate(['apple', 'banana']));
        $this->assertFalse($validator->validate(['apple', 'pear']));
        $this->assertSame(
            "Values must be chosen from 'apple', 'banana'.",
            $validator->getErrorMessage()
        );
    }

    public function testConstructorRejectsEmptyChoices(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The choices list may not be empty.');

        new Choice([]);
    }
}
