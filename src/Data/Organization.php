<?php

namespace Hks\Schema\Data;

use Hks\Schema\Data\Collections\ContactPoints;
use Hks\Schema\Data\Types\EmailAddress;
use Hks\Schema\Data\Types\GeoCoordinate;
use Hks\Schema\Data\Types\PhoneNumber;
use Hks\Schema\Data\Types\PostalAddress;
use Hks\Schema\Data\Types\Url;
use Kirby\Cms\App;
use Kirby\Data\Yaml;

readonly class Organization extends DataObject
{
    public function __construct(
        public string $name,
        public ?string $alternateName = null,
        public ?PostalAddress $address = null,
        public ?GeoCoordinate $coordinates = null,
        public ?EmailAddress $email = null,
        public ?PhoneNumber $telephone = null,
        public ?PhoneNumber $fax = null,
        public ?Url $website = null,
        public ContactPoints $contactPoints = new ContactPoints(),
    ) {
    }

    public static function fromArray(array $value): static
    {
        $value = array_change_key_case($value);

        return new static(
            name: $value['name'] ?? '',
            alternateName: $value['alternatename'] ?? null,
            address: PostalAddress::tryFrom($value['address'] ?? $value),
            coordinates: GeoCoordinate::tryFrom($value['coordinates'] ?? $value),
            email: isset($value['email']) ? EmailAddress::tryFrom($value['email']) : null,
            telephone: isset($value['telephone']) ? PhoneNumber::tryFrom($value['telephone']) : null,
            fax: isset($value['fax']) ? PhoneNumber::tryFrom($value['fax']) : null,
            website: isset($value['website']) ? Url::tryFrom($value['website']) : null,
            contactPoints: ContactPoints::fromArray(Yaml::decode($value['contactpoints'] ?? null)),
        );
    }

    public function name(): string
    {
        return $this->name;
    }

    public function alternateName(): ?string
    {
        return $this->alternateName;
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

    public function contactPoints(): ContactPoints
    {
        return $this->contactPoints;
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
            'alternateName' => $this->alternateName,
            'address' => $this->address?->toArray(),
            'coordinates' => $this->coordinates?->toArray(),
            'email' => $this->email?->toString(),
            'telephone' => $this->telephone?->toString(),
            'fax' => $this->fax?->toString(),
            'website' => $this->website?->toString(),
            'contactPoints' => $this->contactPoints->toArray(),
        ];
    }

    public function toStructuredData(string $type = 'Organization'): array
    {
        return array_filter([
            '@type' => $type,
            'name' => $this->name,
            'alternateName' => $this->alternateName,
            'address' => $this->address?->toStructuredData(),
            'email' => $this->email?->value,
            'telephone' => $this->telephone?->value,
            'faxNumber' => $this->fax?->value,
            'url' => $this->website?->value,
            'contactPoint' => $this->contactPoints->toStructuredData(),
            'location' => $this->address || $this->coordinates ? array_filter([
                '@type' => 'Place',
                'address' => $this->address?->toStructuredData(),
                'geo' => $this->coordinates?->toStructuredData(),
            ]) : null,
        ]);
    }
}
