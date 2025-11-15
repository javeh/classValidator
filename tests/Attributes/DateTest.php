<?php

namespace Javeh\ClassValidator\Tests\Attributes;

use Javeh\ClassValidator\Attributes\Date;
use Javeh\ClassValidator\Tests\TestCase;

class DateTest extends TestCase
{
    public function testValidDateWithinRange(): void
    {
        $validator = new Date(min: '2024-01-01', max: '2024-12-31');

        $this->assertTrue($validator->validate('2024-03-10'));
    }

    public function testRejectsInvalidFormat(): void
    {
        $validator = new Date();

        $this->assertFalse($validator->validate('03-10-2024'));
        $this->assertSame('The value must be a valid date using format Y-m-d.', $validator->getErrorMessage());
    }

    public function testRejectsDateBeforeMinimum(): void
    {
        $validator = new Date(min: '2024-01-01');

        $this->assertFalse($validator->validate('2023-12-31'));
        $this->assertSame('The date must be after 2024-01-01.', $validator->getErrorMessage());
    }

    public function testRejectsAfterMaximum(): void
    {
        $validator = new Date(max: '2024-01-31');

        $this->assertFalse($validator->validate('2024-02-01'));
        $this->assertSame('The date must be before 2024-01-31.', $validator->getErrorMessage());
    }
}
