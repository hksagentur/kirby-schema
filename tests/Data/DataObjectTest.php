<?php

namespace Hks\Schema\Data;

use Hks\Schema\Data\Types\EmailAddress;
use Hks\Schema\Data\Types\PersonName;
use Hks\Schema\TestCase;
use InvalidArgumentException;

class DataObjectTest extends TestCase
{
    public function testFromArrayCoercesValuesThroughTheMatchingDataTypeByConstructorParameterName(): void
    {
        $person = Person::fromArray([
            'name' => ['displayName' => 'Jane Doe'],
            'email' => 'jane@example.com',
        ]);

        $this->assertTrue($person->name->isEqualTo(new PersonName('Jane Doe')));
        $this->assertTrue($person->email->isEqualTo(new EmailAddress('jane@example.com')));
    }

    public function testFromArrayBuildsTheNameFromFlatGivenAndFamilyNameKeys(): void
    {
        // `person.yml`'s `name` fields flatten directly onto the person (a `group`, not an
        // `object`), so the panel never produces a nested `name` key — only sibling
        // `givenName`/`familyName` keys alongside `email`, `telephone`, etc.
        $person = Person::fromArray([
            'givenName' => 'Jane',
            'familyName' => 'Doe',
            'email' => 'jane@example.com',
        ]);

        $this->assertTrue($person->name->isEqualTo(new PersonName(displayName: 'Jane Doe', givenName: 'Jane', familyName: 'Doe')));
        $this->assertTrue($person->email->isEqualTo(new EmailAddress('jane@example.com')));
    }

    public function testFromArrayLeavesOmittedOptionalParametersAtTheirConstructorDefault(): void
    {
        $person = Person::fromArray([
            'name' => ['displayName' => 'Jane Doe'],
        ]);

        $this->assertNull($person->email);
        $this->assertNull($person->telephone);
    }

    public function testFromArrayPassesThroughValuesForNonDataTypeParameters(): void
    {
        $organization = Organization::fromArray([
            'name' => 'Acme Inc.',
        ]);

        $this->assertSame('Acme Inc.', $organization->name);
    }

    public function testFromArrayAcceptsAnAlreadyResolvedDataTypeInstance(): void
    {
        $person = Person::fromArray([
            'name' => new PersonName('Jane Doe'),
        ]);

        $this->assertTrue($person->name->isEqualTo(new PersonName('Jane Doe')));
    }

    public function testFromArrayThrowsWhenARequiredConstructorParameterCannotBeResolved(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Person::fromArray(['email' => 'jane@example.com']);
    }

    public function testFromArrayDoesNotRequireANameWhenTheConstructorParameterIsAPlainString(): void
    {
        // Unlike `Person::$name` (a `PersonName` `DataType`), `Organization::$name` is a
        // plain, unvalidated `string` — a missing name has nothing to fail to resolve.
        $organization = Organization::fromArray([]);

        $this->assertSame('', $organization->name);
    }

    public function testToStructuredDataDefaultsToTheClassesOwnSchemaOrgType(): void
    {
        $organization = new Organization(name: 'Acme Inc.');

        $this->assertSame('Organization', $organization->toStructuredData()['@type']);
    }

    public function testToStructuredDataAcceptsAMoreSpecificSchemaOrgTypeOverride(): void
    {
        $organization = new Organization(name: 'Acme Store');

        $this->assertSame('LocalBusiness', $organization->toStructuredData('LocalBusiness')['@type']);
    }
}
