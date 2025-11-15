<?php

namespace Javeh\ClassValidator\Tests\Attributes;

use Javeh\ClassValidator\Attributes\Url;
use Javeh\ClassValidator\Tests\TestCase;

class UrlTest extends TestCase
{
    public function testAcceptsValidUrl(): void
    {
        $validator = new Url();
        $this->assertTrue($validator->validate('https://example.com'));
    }

    public function testRejectsInvalidUrl(): void
    {
        $validator = new Url();

        $this->assertFalse($validator->validate('not-a-url'));
        $this->assertSame('The value must be a valid URL.', $validator->getErrorMessage());
    }
}
