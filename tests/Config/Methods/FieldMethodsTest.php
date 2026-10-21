<?php

namespace Hks\Schema\Config\Methods;

use Hks\Schema\Data\Exceptions\NotFoundException;
use Hks\Schema\Data\Person;
use Hks\Schema\Data\Types\EmailAddress;
use Hks\Schema\Data\Types\Fraction;
use Hks\Schema\Data\Types\PersonName;
use Hks\Schema\Data\Types\PostalAddress;
use Hks\Schema\Data\Types\Time;
use Hks\Schema\TestCase;
use InvalidArgumentException;
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

    public function testToDataTypeAcceptsAClassStringInsteadOfARegisteredAlias(): void
    {
        $field = new Field(null, 'ratio', '16/9');

        $this->assertTrue(
            $field->toDataType(Fraction::class)->isEqualTo(new Fraction(16, 9))
        );
    }

    public function testToDataTypeThrowsForAnUnregisteredName(): void
    {
        $field = new Field(null, 'email', 'jane@example.com');

        $this->expectException(NotFoundException::class);

        $field->toDataType('does-not-exist');
    }

    public function testToDataTypeHandlesAFieldTheContentFileDoesNotSetAtAll(): void
    {
        // A missing content key gives the Field a `null` raw value — an
        // empty field is not an error, so this must resolve to `null`
        // rather than throwing, regardless of the type's input shape.
        $scalar = new Field(null, 'email', null);
        $array = new Field(null, 'address', null);

        $this->assertNull($scalar->toDataType('email'));
        $this->assertNull($array->toDataType('postal-address'));
    }

    public function testToDataTypeThrowsWhenTheFieldValueFailsValidation(): void
    {
        $field = new Field(null, 'email', 'not-an-email');

        $this->expectException(InvalidArgumentException::class);

        $field->toDataType('email');
    }

    public function testToDataTypePreservesTheOriginalValidationErrorAsThePreviousException(): void
    {
        $field = new Field(null, 'email', 'not-an-email');

        try {
            $field->toDataType('email');
            $this->fail('Expected an exception to be thrown.');
        } catch (InvalidArgumentException $exception) {
            $this->assertNotNull($exception->getPrevious());
            $this->assertStringContainsString('not-an-email', $exception->getPrevious()->getMessage());
        }
    }

    public function testToDataObjectDecodesAnObjectShapedFieldValueByConstructorParameterName(): void
    {
        $field = new Field(null, 'person', "name:\n  displayName: Jane Doe\nemail: jane@example.com\n");

        $person = $field->toDataObject('person');

        $this->assertInstanceOf(Person::class, $person);
        $this->assertTrue($person->name->isEqualTo(new PersonName('Jane Doe')));
        $this->assertTrue($person->email->isEqualTo(new EmailAddress('jane@example.com')));
    }

    public function testToDataObjectThrowsForAnUnregisteredName(): void
    {
        $field = new Field(null, 'person', "name: Jane Doe\n");

        $this->expectException(NotFoundException::class);

        $field->toDataObject('does-not-exist');
    }

    public function testToDataObjectReturnsNullForAnEmptyField(): void
    {
        $field = new Field(null, 'person', '');

        $this->assertNull($field->toDataObject('person'));
    }

    public function testToDataObjectThrowsWhenTheContentFailsValidation(): void
    {
        // Non-empty, but has no "name" — `Person::fromArray()` throws for that.
        $field = new Field(null, 'person', "email: jane@example.com\n");

        $this->expectException(InvalidArgumentException::class);

        $field->toDataObject('person');
    }

    public function testToDataObjectPreservesTheOriginalValidationErrorAsThePreviousException(): void
    {
        // Non-empty, but has no "name" — `Person::fromArray()` throws for that.
        $field = new Field(null, 'person', "email: jane@example.com\n");

        try {
            $field->toDataObject('person');
            $this->fail('Expected an exception to be thrown.');
        } catch (InvalidArgumentException $exception) {
            $this->assertNotNull($exception->getPrevious());
            $this->assertStringContainsString('person name', $exception->getPrevious()->getMessage());
        }
    }

    public function testOxfordListFormatsACommaSeparatedValue(): void
    {
        $field = new Field(null, 'tags', 'Apple, Banana, Cherry');

        $this->assertSame('Apple, Banana, and Cherry', $field->oxfordList(locale: 'en')->value());
    }

    public function testOxfordListTakesTheMethodLimitAndLocalePositionally(): void
    {
        $field = new Field(null, 'tags', 'Apple; Banana; Cherry');

        $this->assertSame('Apple, Banana, and 1 other', $field->oxfordList(';', 2, 'en')->value());
    }

    public function testOxfordListOfAnEmptyFieldIsEmpty(): void
    {
        $this->assertSame('', (new Field(null, 'tags', ''))->oxfordList()->value());
        $this->assertSame('', (new Field(null, 'tags', null))->oxfordList(method: 'yaml')->value());
    }

    public function testOxfordListSplitsOnTheGivenSeparator(): void
    {
        $field = new Field(null, 'tags', 'Apple; Banana');

        $this->assertSame('Apple and Banana', $field->oxfordList(method: ';', locale: 'en')->value());
    }

    public function testOxfordListFormatsAYamlList(): void
    {
        $field = new Field(null, 'qualifications', "- Master of Science\n- Bachelor of Arts, Design\n");

        $this->assertSame(
            'Master of Science and Bachelor of Arts, Design',
            $field->oxfordList(method: 'yaml', locale: 'en')->value(),
        );
    }
}
