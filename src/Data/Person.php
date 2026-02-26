<?php

namespace Hks\Schema\Data;

use Hks\Schema\Data\Contracts\Arrayable;
use Hks\Schema\Data\Contracts\Htmlable;
use Hks\Schema\Data\Contracts\Schemable;
use Hks\Schema\Data\Types\EmailAddress;
use Hks\Schema\Data\Types\ImageUrl;
use Hks\Schema\Data\Types\Markdown;
use Hks\Schema\Data\Types\PersonName;
use Hks\Schema\Data\Types\PhoneNumber;
use Kirby\Cms\App;
use Stringable;

readonly class Person implements Arrayable, Htmlable, Stringable, Schemable
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

    public function toSchema(): array
    {
        return array_filter([
            '@type' => 'Person',
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

    public function toHtml(array $attributes = []): string
    {
        return App::instance()->snippet('schema/person', [
            'item' => $this,
            'attrs' => $attributes,
        ]);
    }

    public function toString(): string
    {
        return $this->toHtml();
    }

    public function __toString(): string
    {
        return $this->toString();
    }
}
