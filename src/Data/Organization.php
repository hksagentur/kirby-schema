<?php

namespace Hks\Schema\Data;

use Hks\Schema\Data\Types\EmailAddress;
use Hks\Schema\Data\Types\GeoCoordinate;
use Hks\Schema\Data\Types\PhoneNumber;
use Hks\Schema\Data\Types\PostalAddress;
use Hks\Schema\Data\Types\Url;
use Kirby\Cms\App;

readonly class Organization extends DataObject
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

    public static function fromArray(array $value): static
    {
        return new static(
            name: $value['name'] ?? '',
            address: isset($value['address']) ? PostalAddress::tryFrom($value['address']) : null,
            coordinates: isset($value['coordinates']) ? GeoCoordinate::tryFrom($value['coordinates']) : null,
            email: isset($value['email']) ? EmailAddress::tryFrom($value['email']) : null,
            telephone: isset($value['telephone']) ? PhoneNumber::tryFrom($value['telephone']) : null,
            fax: isset($value['fax']) ? PhoneNumber::tryFrom($value['fax']) : null,
            website: isset($value['website']) ? Url::tryFrom($value['website']) : null,
        );
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

    public function toHtml(array $attributes = []): string
    {
        return App::instance()->snippet('schema/organization', [
            'item' => $this,
            'attrs' => $attributes,
        ]);
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

    public function toStructuredData(string $type = 'Organization'): array
    {
        return array_filter([
            '@type' => $type,
            'name' => $this->name,
            'address' => $this->address?->toStructuredData(),
            'email' => $this->email?->value,
            'telephone' => $this->telephone?->value,
            'faxNumber' => $this->fax?->value,
            'website' => $this->website?->value,
            'location' => array_filter([
                '@type' => 'Place',
                'address' => $this->address?->toStructuredData(),
                'geo' => $this->coordinates?->toStructuredData(),
            ]),
        ]);
    }
}
