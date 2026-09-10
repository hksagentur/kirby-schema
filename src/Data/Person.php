<?php

namespace Hks\Schema\Data;

use Hks\Schema\Cms\Find;
use Hks\Schema\Data\Types\EmailAddress;
use Hks\Schema\Data\Types\Markup;
use Hks\Schema\Data\Types\PersonName;
use Hks\Schema\Data\Types\PhoneNumber;
use Kirby\Cms\App;
use Kirby\Cms\File;

readonly class Person extends DataObject
{
    /**
     * @param string[] $qualifications
     * @param string[] $expertise
     */
    public function __construct(
        public PersonName $name,
        public ?File $image = null,
        public ?Markup $description = null,
        public ?EmailAddress $email = null,
        public ?PhoneNumber $telephone = null,
        public ?PhoneNumber $fax = null,
        public ?string $jobTitle = null,
        public array $qualifications = [],
        public array $expertise = [],
    ) {
    }

    public static function fromArray(array $value): static
    {
        $value = array_change_key_case($value);

        return new static(
            name: PersonName::from($value['name'] ?? $value),
            image: isset($value['image']) ? Find::file($value['image']) : null,
            description: isset($value['description']) ? Markup::tryFrom($value['description']) : null,
            email: isset($value['email']) ? EmailAddress::tryFrom($value['email']) : null,
            telephone: isset($value['telephone']) ? PhoneNumber::tryFrom($value['telephone']) : null,
            fax: isset($value['fax']) ? PhoneNumber::tryFrom($value['fax']) : null,
            jobTitle: $value['jobtitle'] ?? null,
            qualifications: $value['qualifications'] ?? [],
            expertise: $value['expertise'] ?? [],
        );
    }

    public function name(): PersonName
    {
        return $this->name;
    }

    public function image(): ?File
    {
        return $this->image;
    }

    public function description(): ?Markup
    {
        return $this->description;
    }

    public function email(): ?EmailAddress
    {
        return $this->email;
    }

    public function telephone(): ?PhoneNumber
    {
        return $this->telephone;
    }

    public function fax(): ?PhoneNumber
    {
        return $this->fax;
    }

    public function jobTitle(): ?string
    {
        return $this->jobTitle;
    }

    /** @return array<int, string> */
    public function qualifications(): array
    {
        return $this->qualifications;
    }

    /** @return array<int, string> */
    public function expertise(): array
    {
        return $this->expertise;
    }

    public function toHtml(array $attributes = []): string
    {
        return App::instance()->snippet('schema/person', [
            'item' => $this,
            'attrs' => $attributes,
        ]);
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name->toArray(),
            'description' => $this->description?->toString(),
            'image' => $this->image?->uuid()?->toString(),
            'email' => $this->email?->toString(),
            'telephone' => $this->telephone?->toString(),
            'fax' => $this->fax?->toString(),
            'jobTitle' => $this->jobTitle,
            'qualifications' => $this->qualifications,
            'expertise' => $this->expertise,
        ];
    }

    public function toStructuredData(string $type = 'Person'): array
    {
        return array_filter([
            '@type' => $type,
            'name' => $this->name->displayName,
            'givenName' => $this->name->givenName,
            'additionalName' => $this->name->additionalName,
            'familyName' => $this->name->familyName,
            'honorificPrefix' => $this->name->honorificPrefix,
            'image' => $this->image?->url(),
            'email' => $this->email?->value,
            'telephone' => $this->telephone?->value,
            'faxNumber' => $this->fax?->value,
            'jobTitle' => $this->jobTitle,
            'knowsAbout' => $this->expertise,
            'hasCredential' => array_map(
                fn (string $qualification) => [
                    '@type' => 'EducationalOccupationalCredential',
                    'name' => $qualification,
                ],
                $this->qualifications,
            ),
        ]);
    }
}
