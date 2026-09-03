<?php

namespace Hks\Schema\Data\Types;

use Hks\Schema\TestCase;

class DataTypeTest extends TestCase
{
    public function testAcceptsArrayInputIsTrueForATypeMarkedWithTheAttribute(): void
    {
        $this->assertTrue(PostalAddress::acceptsArrayInput());
    }

    public function testAcceptsArrayInputIsFalseForATypeWithoutTheAttribute(): void
    {
        $this->assertFalse(EmailAddress::acceptsArrayInput());
        $this->assertFalse(Time::acceptsArrayInput());
    }
}
