<?php

namespace Hks\Schema\Data;

use Hks\Schema\Formatter\OrganizationHtmlFormatter;
use Hks\Schema\Formatter\OrganizationSchemaTransformer;
use Hks\Schema\Formatter\OrganizationTextFormatter;

readonly class Organization extends DataObject
{
    public function __construct(
        public Name $name,
        public ?PostalAddress $address = null,
        public ?GeoCoordinate $coordinates = null,
        public ?Email $email = null,
        public ?PhoneNumber $telephone = null,
        public ?PhoneNumber $fax = null,
        public ?Url $website = null,
    ) {
    }

    public static function fromArray(array $attributes): static
    {
        return new static(
            name: Name::from($attributes['name']),
            address: PostalAddress::tryFromArray($attributes['address'] ?? null),
            coordinates: GeoCoordinate::tryFromArray($attributes['coordinates'] ?? null),
            email: Email::tryFrom($attributes['email'] ?? null),
            telephone: PhoneNumber::tryFrom($attributes['telephone'] ?? null),
            fax: PhoneNumber::tryFrom($attributes['fax'] ?? null),
            website: Url::tryFrom($attributes['website']),
        );
    }

    public function name(): ?Name
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

    public function email(): ?Email
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
            'name' => $this->name?->toString(),
            'address' => $this->address?->toArray(),
            'coordinates' => $this->coordinates?->toArray(),
            'email' => $this->email?->toString(),
            'telephone' => $this->telephone?->toString(),
            'fax' => $this->fax?->toString(),
            'website' => $this->website?->toString(),
        ];
    }

    public function toString(): string
    {
        return $this->format(OrganizationTextFormatter::class);
    }

    public function toHtml(array $attributes = []): string
    {
        return $this->format(OrganizationHtmlFormatter::class, ['attributes' => $attributes]);
    }

    public function toSchema(): array
    {
        return $this->transform(OrganizationSchemaTransformer::class);
    }
}
