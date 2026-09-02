<?php

namespace Hks\Schema\Data;

use Hks\Schema\Data\Types\EmailAddress;
use Hks\Schema\Data\Types\PersonName;
use Hks\Schema\TestCase;

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
