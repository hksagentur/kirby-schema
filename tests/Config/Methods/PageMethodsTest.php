<?php

namespace Hks\Schema\Config\Methods;

use Hks\Schema\Data\Person;
use Hks\Schema\TestCase;
use Kirby\Cms\Page;

class PageMethodsTest extends TestCase
{
    public function testToDataObjectResolvesThePagesOwnContentFieldsByConstructorParameterName(): void
    {
        $page = new Page([
            'slug' => 'jane-doe',
            'content' => [
                'name' => 'Jane Doe',
                'email' => 'jane@example.com',
            ],
        ]);

        $person = $page->toDataObject('person');

        $this->assertInstanceOf(Person::class, $person);
        $this->assertSame('Jane Doe', $person->name->displayName);
    }

    public function testToDataObjectReturnsNullForAnUnregisteredName(): void
    {
        $page = new Page([
            'slug' => 'jane-doe',
            'content' => ['name' => 'Jane Doe'],
        ]);

        $this->assertNull($page->toDataObject('does-not-exist'));
    }

    public function testToDataObjectReturnsNullInsteadOfCrashingWhenARequiredConstructorParameterIsMissing(): void
    {
        // Has content, but no "name" — `Person::fromArray()` throws for that.
        $page = new Page([
            'slug' => 'jane-doe',
            'content' => ['email' => 'jane@example.com'],
        ]);

        $this->assertNull($page->toDataObject('person'));
    }
}
