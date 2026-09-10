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

    public function testToDataObjectResolvesJobTitleDespitePageContentLowercasingItsKey(): void
    {
        // `Page::content()->toArray()` (what `page.php`'s `toDataObject()` feeds `Person::fromArray()`)
        // lowercases every top-level key — `jobTitle` only survives this round trip because
        // `Person::fromArray()` normalizes casing itself before reading it.
        $content = static::contentFromBlueprint('@hksagentur/schema/fields/person', [
            'givenName' => 'Jane',
            'familyName' => 'Doe',
            'jobTitle' => 'Senior Web Developer',
            'qualifications' => ['Master of Science Digital Media'],
            'expertise' => ['Kirby CMS'],
        ]);

        $page = new Page(['slug' => 'jane-doe', 'content' => $content]);

        $person = $page->toDataObject('person');

        $this->assertSame('Senior Web Developer', $person->jobTitle());
        $this->assertSame(['Master of Science Digital Media'], $person->qualifications());
        $this->assertSame(['Kirby CMS'], $person->expertise());
    }
}
