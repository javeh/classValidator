<?php

namespace Javeh\ClassValidator\Tests\Attributes;

use Javeh\ClassValidator\Attributes\Regex;
use Javeh\ClassValidator\Tests\TestCase;

class RegexTest extends TestCase
{
    public function testAcceptsValueMatchingPattern(): void
    {
        $validator = new Regex('/^foo/');
        $this->assertTrue($validator->validate('foobar'));
    }

    public function testRejectsValueBreakingPattern(): void
    {
        $validator = new Regex('/^foo/');

        $this->assertFalse($validator->validate('bar'));
        $this->assertSame('The value must match the required pattern.', $validator->getErrorMessage());
    }

    public function testThrowsOnInvalidPattern(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid regex pattern: /(');

        new Regex('/(');
    }
}
