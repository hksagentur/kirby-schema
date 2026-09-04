<?php

namespace Hks\Schema\Data;

use Hks\Schema\Data\Types\GeoCoordinate;
use Hks\Schema\Data\Types\PostalAddress;
use Kirby\Cms\App;

readonly class Place extends DataObject
{
    public function __construct(
        public string $name,
        public ?PostalAddress $address = null,
        public ?GeoCoordinate $coordinates = null,
    ) {
    }

    public static function fromArray(array $value): static
    {
        return new static(
            name: $value['name'] ?? '',
            address: PostalAddress::tryFrom($value['address'] ?? $value),
            coordinates: GeoCoordinate::tryFrom($value['coordinates'] ?? $value),
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

    public function toHtml(array $attributes = []): string
    {
        return App::instance()->snippet('schema/place', [
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
        ];
    }

    public function toStructuredData(string $type = 'Place'): array
    {
        return array_filter([
            '@type' => $type,
            'name' => $this->name,
            'address' => $this->address?->toStructuredData(),
            'geo' => $this->coordinates?->toStructuredData(),
        ]);
    }
}
