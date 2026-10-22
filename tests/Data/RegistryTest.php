<?php

namespace Hks\Schema\Data;

use Hks\Schema\Data\Types\EmailAddress;
use Hks\Schema\Data\Types\Fraction;
use Hks\Schema\TestCase;
use stdClass;

class RegistryTest extends TestCase
{
    public function testDataTypeResolvesARegisteredNameToItsClass(): void
    {
        $this->assertSame(EmailAddress::class, Registry::dataType('email-address'));
    }

    public function testDataTypeReturnsNullForAnUnregisteredName(): void
    {
        $this->assertNull(Registry::dataType('does-not-exist'));
    }

    public function testDataTypeAcceptsAClassStringInsteadOfARegisteredAlias(): void
    {
        $this->assertSame(Fraction::class, Registry::dataType(Fraction::class));
    }

    public function testDataTypeReturnsNullForAClassStringThatIsNotADataType(): void
    {
        $this->assertNull(Registry::dataType(stdClass::class));
    }

    public function testDataObjectResolvesARegisteredNameToItsClass(): void
    {
        $this->assertSame(Person::class, Registry::dataObject('person'));
    }

    public function testDataObjectReturnsNullForAnUnregisteredName(): void
    {
        $this->assertNull(Registry::dataObject('does-not-exist'));
    }

    public function testDataObjectAcceptsAClassStringInsteadOfARegisteredAlias(): void
    {
        $this->assertSame(Person::class, Registry::dataObject(Person::class));
    }

    public function testDataObjectReturnsNullForAClassStringThatIsNotADataObject(): void
    {
        $this->assertNull(Registry::dataObject(stdClass::class));
    }
}
