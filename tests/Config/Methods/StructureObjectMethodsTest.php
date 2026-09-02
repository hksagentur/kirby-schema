<?php

namespace Hks\Schema\Config\Methods;

use Hks\Schema\Data\Person;
use Hks\Schema\TestCase;
use Kirby\Cms\StructureObject;

class StructureObjectMethodsTest extends TestCase
{
    public function testToDataObjectResolvesTheStructureObjectsOwnFieldValuesByConstructorParameterName(): void
    {
        $structureObject = new StructureObject([
            'content' => [
                'name' => 'Jane Doe',
                'email' => 'jane@example.com',
            ],
        ]);

        $person = $structureObject->toDataObject('person');

        $this->assertInstanceOf(Person::class, $person);
        $this->assertSame('Jane Doe', $person->name->displayName);
    }

    public function testToDataObjectReturnsNullForAnUnregisteredName(): void
    {
        $structureObject = new StructureObject([
            'content' => ['name' => 'Jane Doe'],
        ]);

        $this->assertNull($structureObject->toDataObject('does-not-exist'));
    }

    public function testToDataObjectReturnsNullInsteadOfCrashingWhenARequiredConstructorParameterIsMissing(): void
    {
        // Has content, but no "name" — `Person::fromArray()` throws for that.
        $structureObject = new StructureObject([
            'content' => ['email' => 'jane@example.com'],
        ]);

        $this->assertNull($structureObject->toDataObject('person'));
    }
}
