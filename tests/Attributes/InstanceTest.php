<?php

namespace Javeh\ClassValidator\Tests\Attributes;

use Javeh\ClassValidator\Attributes\Instance;
use Javeh\ClassValidator\Tests\TestCase;

class InstanceTest extends TestCase
{
    public function testValidInstance(): void
    {
        $validator = new Instance(\stdClass::class);
        $this->assertTrue($validator->validate(new \stdClass()));
    }

    public function testRejectsNonObject(): void
    {
        $validator = new Instance(\stdClass::class);

        $this->assertFalse($validator->validate('string'));
        $this->assertSame('The value must be an object.', $validator->getErrorMessage());
    }

    public function testRejectsWrongClass(): void
    {
        $validator = new Instance(\ArrayObject::class);

        $this->assertFalse($validator->validate(new \stdClass()));
        $this->assertSame(
            'The value is an instance of stdClass but must be an instance of ArrayObject.',
            $validator->getErrorMessage()
        );
    }

    public function testConstructorRejectsUnknownClass(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The class or interface "MissingClass" does not exist.');

        new Instance('MissingClass');
    }
}
