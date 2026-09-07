<?php

namespace Hks\Schema\Config\Methods;

use Hks\Schema\Data\Exceptions\NotFoundException;
use Hks\Schema\Data\Person;
use Hks\Schema\TestCase;
use InvalidArgumentException;
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

    public function testToDataObjectThrowsForAnUnregisteredName(): void
    {
        $structureObject = new StructureObject([
            'content' => ['name' => 'Jane Doe'],
        ]);

        $this->expectException(NotFoundException::class);

        $structureObject->toDataObject('does-not-exist');
    }

    public function testToDataObjectThrowsWhenTheContentFailsValidation(): void
    {
        // Has content, but no "name" — `Person::fromArray()` throws for that.
        $structureObject = new StructureObject([
            'content' => ['email' => 'jane@example.com'],
        ]);

        $this->expectException(InvalidArgumentException::class);

        $structureObject->toDataObject('person');
    }

    public function testToDataObjectPreservesTheOriginalValidationErrorAsThePreviousException(): void
    {
        // Has content, but no "name" — `Person::fromArray()` throws for that.
        $structureObject = new StructureObject([
            'content' => ['email' => 'jane@example.com'],
        ]);

        try {
            $structureObject->toDataObject('person');
            $this->fail('Expected an exception to be thrown.');
        } catch (InvalidArgumentException $exception) {
            $this->assertNotNull($exception->getPrevious());
            $this->assertStringContainsString('person name', $exception->getPrevious()->getMessage());
        }
    }
}
