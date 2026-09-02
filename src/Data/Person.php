<?php

namespace Hks\Schema\Data;

use Hks\Schema\Data\Types\EmailAddress;
use Hks\Schema\Data\Types\ImageUrl;
use Hks\Schema\Data\Types\Markdown;
use Hks\Schema\Data\Types\PersonName;
use Hks\Schema\Data\Types\PhoneNumber;
use Kirby\Cms\App;

readonly class Person extends DataObject
{
    public function __construct(
        public PersonName $name,
        public ?ImageUrl $image = null,
        public ?Markdown $description = null,
        public ?EmailAddress $email = null,
        public ?PhoneNumber $telephone = null,
        public ?PhoneNumber $fax = null,
    ) {
    }

    public static function fromArray(array $value): static
    {
        return new static(
            name: PersonName::tryFrom($value['name'] ?? ''),
            image: isset($value['image']) ? ImageUrl::tryFrom($value['image']) : null,
            description: isset($value['description']) ? Markdown::tryFrom($value['description']) : null,
            email: isset($value['email']) ? EmailAddress::tryFrom($value['email']) : null,
            telephone: isset($value['telephone']) ? PhoneNumber::tryFrom($value['telephone']) : null,
            fax: isset($value['fax']) ? PhoneNumber::tryFrom($value['fax']) : null,
        );
    }

    public function name(): PersonName
    {
        return $this->name;
    }

    public function image(): ?ImageUrl
    {
        return $this->image;
    }

    public function description(): ?Markdown
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
            'image' => $this->image?->toString(),
            'email' => $this->email?->toString(),
            'telephone' => $this->telephone?->toString(),
            'fax' => $this->fax?->toString(),
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
            'email' => $this->email?->value,
            'telephone' => $this->telephone?->value,
            'faxNumber' => $this->fax?->value,
        ]);
    }
}
