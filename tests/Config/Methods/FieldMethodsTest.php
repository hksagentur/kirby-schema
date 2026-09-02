<?php

namespace Hks\Schema\Config\Methods;

use Hks\Schema\Data\Person;
use Hks\Schema\Data\Types\EmailAddress;
use Hks\Schema\Data\Types\PersonName;
use Hks\Schema\Data\Types\PostalAddress;
use Hks\Schema\Data\Types\Time;
use Hks\Schema\TestCase;
use Kirby\Content\Field;

class FieldMethodsTest extends TestCase
{
    public function testToDataTypeResolvesAScalarFieldValueThroughTheMatchingType(): void
    {
        $field = new Field(null, 'email', 'jane@example.com');

        $this->assertTrue(
            $field->toDataType('email')->isEqualTo(new EmailAddress('jane@example.com'))
        );
    }

    public function testToDataTypePassesTheRawScalarValueEvenWhenTheTypeAlsoAcceptsArrayInput(): void
    {
        $field = new Field(null, 'time', '09:00');

        $this->assertInstanceOf(Time::class, $field->toDataType('time'));
    }

    public function testToDataTypeDecodesAnObjectShapedFieldValueForAnArrayOnlyType(): void
    {
        $field = new Field(null, 'address', "streetAddress: Main St 1\naddressLocality: Springfield\n");

        $address = $field->toDataType('postal-address');

        $this->assertInstanceOf(PostalAddress::class, $address);
        $this->assertSame('Main St 1', $address->streetAddress());
        $this->assertSame('Springfield', $address->locality());
    }

    public function testToDataTypeReturnsNullForAnUnregisteredName(): void
    {
        $field = new Field(null, 'email', 'jane@example.com');

        $this->assertNull($field->toDataType('does-not-exist'));
    }

    public function testToDataTypeHandlesAFieldTheContentFileDoesNotSetAtAll(): void
    {
        // A missing content key gives the Field a `null` raw value, which
        // neither a scalar-only nor an array-only `tryFrom()` accepts as-is —
        // both must fall through to a graceful `null` rather than crash.
        $scalar = new Field(null, 'email', null);
        $array = new Field(null, 'address', null);

        $this->assertNull($scalar->toDataType('email'));
        $this->assertNull($array->toDataType('postal-address'));
    }

    public function testToDataObjectDecodesAnObjectShapedFieldValueByConstructorParameterName(): void
    {
        $field = new Field(null, 'person', "name:\n  displayName: Jane Doe\nemail: jane@example.com\n");

        $person = $field->toDataObject('person');

        $this->assertInstanceOf(Person::class, $person);
        $this->assertTrue($person->name->isEqualTo(new PersonName('Jane Doe')));
        $this->assertTrue($person->email->isEqualTo(new EmailAddress('jane@example.com')));
    }

    public function testToDataObjectReturnsNullForAnUnregisteredName(): void
    {
        $field = new Field(null, 'person', '');

        $this->assertNull($field->toDataObject('does-not-exist'));
    }

    public function testToDataObjectReturnsNullInsteadOfCrashingWhenARequiredConstructorParameterIsMissing(): void
    {
        // Non-empty, but has no "name" — `Person::fromArray()` throws for that.
        $field = new Field(null, 'person', "email: jane@example.com\n");

        $this->assertNull($field->toDataObject('person'));
    }
}
