<?php

namespace Hks\Schema\Config\Methods;

use Hks\Schema\Data\Exceptions\NotFoundException;
use Hks\Schema\Data\Person;
use Hks\Schema\TestCase;
use InvalidArgumentException;
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

    public function testToDataObjectThrowsForAnUnregisteredName(): void
    {
        $page = new Page([
            'slug' => 'jane-doe',
            'content' => ['name' => 'Jane Doe'],
        ]);

        $this->expectException(NotFoundException::class);

        $page->toDataObject('does-not-exist');
    }

    public function testToDataObjectThrowsWhenTheContentFailsValidation(): void
    {
        // Has content, but no "name" — `Person::fromArray()` throws for that.
        $page = new Page([
            'slug' => 'jane-doe',
            'content' => ['email' => 'jane@example.com'],
        ]);

        $this->expectException(InvalidArgumentException::class);

        $page->toDataObject('person');
    }

    public function testToDataObjectPreservesTheOriginalValidationErrorAsThePreviousException(): void
    {
        // Has content, but no "name" — `Person::fromArray()` throws for that.
        $page = new Page([
            'slug' => 'jane-doe',
            'content' => ['email' => 'jane@example.com'],
        ]);

        try {
            $page->toDataObject('person');
            $this->fail('Expected an exception to be thrown.');
        } catch (InvalidArgumentException $exception) {
            $this->assertNotNull($exception->getPrevious());
            $this->assertStringContainsString('person name', $exception->getPrevious()->getMessage());
        }
    }
}
