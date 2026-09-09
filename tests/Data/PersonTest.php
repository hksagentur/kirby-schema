<?php

namespace Hks\Schema\Data;

use Hks\Schema\TestCase;
use Kirby\Cms\App;
use Kirby\Cms\File;

class PersonTest extends TestCase
{
    protected function setUp(): void
    {
        App::$enableWhoops = false;
    }

    protected function tearDown(): void
    {
        new App(['roots' => ['index' => dirname(__DIR__)]]);
        App::$enableWhoops = true;
    }

    public function testResolvesImageToAFileWhenTheReferenceExists(): void
    {
        new App([
            'roots' => ['index' => dirname(__DIR__)],
            'site' => [
                'children' => [
                    [
                        'slug' => 'jane-doe',
                        'files' => [
                            ['filename' => 'portrait.jpg'],
                        ],
                    ],
                ],
            ],
        ]);

        $person = Person::fromArray([
            'givenName' => 'Jane',
            'familyName' => 'Doe',
            'image' => 'jane-doe/portrait.jpg',
        ]);

        $this->assertInstanceOf(File::class, $person->image());
        $this->assertSame('portrait.jpg', $person->image()->filename());
    }

    public function testResolvesImageToNullWhenNoImageIsProvided(): void
    {
        $person = Person::fromArray([
            'givenName' => 'Jane',
            'familyName' => 'Doe',
        ]);

        $this->assertNull($person->image());
    }

    public function testResolvesImageToNullWhenTheReferenceCannotBeResolved(): void
    {
        $person = Person::fromArray([
            'givenName' => 'Jane',
            'familyName' => 'Doe',
            'image' => 'file://does-not-exist',
        ]);

        $this->assertNull($person->image());
    }
}
