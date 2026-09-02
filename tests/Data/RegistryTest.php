<?php

namespace Hks\Schema\Data;

use Hks\Schema\Data\Types\EmailAddress;
use Hks\Schema\Data\Types\PostalAddress;
use Hks\Schema\Data\Types\Time;
use Hks\Schema\TestCase;

class RegistryTest extends TestCase
{
    public function testDataTypeResolvesARegisteredNameToItsClass(): void
    {
        $this->assertSame(EmailAddress::class, Registry::dataType('email'));
    }

    public function testDataTypeReturnsNullForAnUnregisteredName(): void
    {
        $this->assertNull(Registry::dataType('does-not-exist'));
    }

    public function testDataObjectResolvesARegisteredNameToItsClass(): void
    {
        $this->assertSame(Person::class, Registry::dataObject('person'));
    }

    public function testDataObjectReturnsNullForAnUnregisteredName(): void
    {
        $this->assertNull(Registry::dataObject('does-not-exist'));
    }

    public function testAcceptsArrayInputIsTrueForATypeMarkedWithTheAttribute(): void
    {
        $this->assertTrue(Registry::acceptsArrayInput(PostalAddress::class));
    }

    public function testAcceptsArrayInputIsFalseForATypeWithoutTheAttribute(): void
    {
        $this->assertFalse(Registry::acceptsArrayInput(EmailAddress::class));
        $this->assertFalse(Registry::acceptsArrayInput(Time::class));
    }
}
