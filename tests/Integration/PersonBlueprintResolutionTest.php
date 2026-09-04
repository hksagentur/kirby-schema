<?php

namespace Hks\Schema\Integration;

use Hks\Schema\Data\Person;
use Hks\Schema\TestCase;
use Kirby\Cms\Page;

class PersonBlueprintResolutionTest extends TestCase
{
    use InteractsWithBlueprintFields;

    public function testToDataObjectResolvesAPersonFromContentShapedByItsOwnBlueprint(): void
    {
        $content = static::contentFromBlueprint('@hksagentur/schema/fields/person', [
            'givenName' => 'Jane',
            'additionalName' => 'Q.',
            'familyName' => 'Doe',
            'email' => 'jane@example.com',
            'telephone' => '+49 30 1234567',
        ]);

        $page = new Page(['slug' => 'jane-doe', 'content' => $content]);

        $person = $page->toDataObject('person');

        $this->assertInstanceOf(Person::class, $person);
        $this->assertSame('Jane Q. Doe', $person->name->displayName);
        $this->assertSame('Jane', $person->name->givenName);
        $this->assertSame('Doe', $person->name->familyName);
        $this->assertSame('jane@example.com', $person->email()?->value);
        $this->assertNotNull($person->telephone());
    }
}
