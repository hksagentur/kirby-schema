<?php

namespace Hks\Schema\Data;

use Hks\Schema\Concerns\InteractsWithTmp;
use Hks\Schema\TestCase;
use Kirby\Cms\App;
use Kirby\Cms\File;

class PersonTest extends TestCase
{
    use InteractsWithTmp;

    public const TMP = __DIR__ . '/tmp/PersonTest';

    public function testResolvesImageToAFileWhenTheReferenceExists(): void
    {
        $this->app = new App([
            'roots' => [
                'index' => static::TMP,
            ],
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


    public function testResolvesJobTitleQualificationsAndExpertise(): void
    {
        $person = Person::fromArray([
            'givenName' => 'Jane',
            'familyName' => 'Doe',
            'jobTitle' => 'Senior Web Developer',
            'qualifications' => ['Master of Science Digital Media'],
            'expertise' => ['Kirby CMS', 'Accessibility'],
        ]);

        $this->assertSame('Senior Web Developer', $person->jobTitle());
        $this->assertSame(['Master of Science Digital Media'], $person->qualifications());
        $this->assertSame(['Kirby CMS', 'Accessibility'], $person->expertise());
    }

    public function testResolvesJobTitleFromALowercasedKey(): void
    {
        // `Page::content()->toArray()`/`StructureObject::toArray()` lowercase top-level content
        // keys — `jobTitle` is the one multi-word key `Person::fromArray()` itself reads.
        $person = Person::fromArray([
            'givenname' => 'Jane',
            'familyname' => 'Doe',
            'jobtitle' => 'Senior Web Developer',
        ]);

        $this->assertSame('Senior Web Developer', $person->jobTitle());
    }

    public function testJobTitleQualificationsAndExpertiseDefaultToEmpty(): void
    {
        $person = Person::fromArray([
            'givenName' => 'Jane',
            'familyName' => 'Doe',
        ]);

        $this->assertNull($person->jobTitle());
        $this->assertSame([], $person->qualifications());
        $this->assertSame([], $person->expertise());
    }

    public function testToStructuredDataMapsQualificationsToCredentialNodes(): void
    {
        $person = Person::fromArray([
            'givenName' => 'Jane',
            'familyName' => 'Doe',
            'jobTitle' => 'Senior Web Developer',
            'qualifications' => ['Master of Science Digital Media'],
            'expertise' => ['Kirby CMS'],
        ]);

        $data = $person->toStructuredData();

        $this->assertSame('Senior Web Developer', $data['jobTitle']);
        $this->assertSame([
            ['@type' => 'EducationalOccupationalCredential', 'name' => 'Master of Science Digital Media'],
        ], $data['hasCredential']);
        $this->assertSame(['Kirby CMS'], $data['knowsAbout']);
    }

    public function testToStructuredDataOmitsJobTitleQualificationsAndExpertiseWhenAbsent(): void
    {
        $person = Person::fromArray([
            'givenName' => 'Jane',
            'familyName' => 'Doe',
        ]);

        $data = $person->toStructuredData();

        $this->assertArrayNotHasKey('jobTitle', $data);
        $this->assertArrayNotHasKey('hasCredential', $data);
        $this->assertArrayNotHasKey('knowsAbout', $data);
    }

    public function testDecodesQualificationsAndExpertiseStoredAsYaml(): void
    {
        $person = Person::fromArray([
            'name' => 'Jane Doe',
            'qualifications' => "- Master of Science Digital Media",
            'expertise' => "- Kirby CMS\n- Accessibility",
        ]);

        $this->assertSame(['Master of Science Digital Media'], $person->qualifications());
        $this->assertSame(['Kirby CMS', 'Accessibility'], $person->expertise());
    }
}
