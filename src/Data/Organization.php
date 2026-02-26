<?php

namespace Hks\Schema\Data;

use Hks\Schema\Data\Contracts\Arrayable;
use Hks\Schema\Data\Contracts\Htmlable;
use Hks\Schema\Data\Contracts\Schemable;
use Hks\Schema\Data\Types\EmailAddress;
use Hks\Schema\Data\Types\GeoCoordinate;
use Hks\Schema\Data\Types\PhoneNumber;
use Hks\Schema\Data\Types\PostalAddress;
use Hks\Schema\Data\Types\Url;
use Kirby\Cms\App;
use Stringable;

readonly class Organization implements Arrayable, Htmlable, Stringable, Schemable
{
    public function __construct(
        public string $name,
        public ?PostalAddress $address = null,
        public ?GeoCoordinate $coordinates = null,
        public ?EmailAddress $email = null,
        public ?PhoneNumber $telephone = null,
        public ?PhoneNumber $fax = null,
        public ?Url $website = null,
    ) {
    }

    public function name(): string
    {
        return $this->name;
    }

    public function address(): ?PostalAddress
    {
        return $this->address;
    }

    public function coordinates(): ?GeoCoordinate
    {
        return $this->coordinates;
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

    public function website(): ?Url
    {
        return $this->website;
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'address' => $this->address?->toArray(),
            'coordinates' => $this->coordinates?->toArray(),
            'email' => $this->email?->toString(),
            'telephone' => $this->telephone?->toString(),
            'fax' => $this->fax?->toString(),
            'website' => $this->website?->toString(),
        ];
    }

    public function toSchema(): array
    {
        return array_filter([
            '@type' => 'Organization',
            'name' => $this->name,
            'address' => $this->address?->toSchema(),
            'email' => $this->email?->value,
            'telephone' => $this->telephone?->value,
            'faxNumber' => $this->fax?->value,
            'website' => $this->website?->value,
            'location' => array_filter([
                '@type' => 'Place',
                'address' => $this->address?->toSchema(),
                'geo' => $this->coordinates?->toSchema(),
            ]),
        ]);
    }

    public function toHtml(array $attributes = []): string
    {
        return App::instance()->snippet('schema/organization', [
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
