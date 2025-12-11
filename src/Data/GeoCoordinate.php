<?php

namespace Hks\Schema\Data;

use Hks\Schema\Formatter\GeoCoordinateHtmlFormatter;
use Hks\Schema\Formatter\GeoCoordinateSchemaTransformer;
use Hks\Schema\Formatter\GeoCoordinateTextFormatter;
use InvalidArgumentException;
use Kirby\Geo\Point;
use Kirby\Toolkit\Str;

readonly class GeoCoordinate extends CompositeDataValue
{
    public function __construct(
        public float $latitude,
        public float $longitude,
    ) {
        $this->validate();
    }

    public static function from(float $latitude, float $longitude): static
    {
        return new static($latitude, $longitude);
    }

    public static function tryFrom(?float $latitude, ?float $longitude): ?static
    {
        try {
            return isset($latitude, $longitude) ? static::from($latitude, $longitude) : null;
        } catch (InvalidArgumentException) {
            return null;
        }
    }

    public static function fromString(string $value): static
    {
        $parts = Str::split($value, ',');

        if (count($parts) !== 2) {
            throw new InvalidArgumentException(sprintf(
                'The given value "%s" does not represent a valid geographic coordinate.',
                $value,
            ));
        }

        return new static(
            latitude: (float) $parts[0],
            longitude: (float) $parts[1],
        );
    }

    public static function fromArray(array $attributes): static
    {
        return new static(
            latitude: $attributes['latitude'] ?? $attributes['lat'] ?? $attributes[0],
            longitude: $attributes['longitude'] ?? $attributes['lng'] ?? $attributes[1],
        );
    }

    public function latitude(): float
    {
        return $this->latitude;
    }

    public function longitude(): float
    {
        return $this->longitude;
    }

    public function toArray(): array
    {
        return [
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
        ];
    }

    public function toString(): string
    {
        return $this->format(GeoCoordinateTextFormatter::class);
    }

    public function toHtml(array $attributes = []): string
    {
        return $this->format(GeoCoordinateHtmlFormatter::class, ['attributes' => $attributes]);
    }

    public function toSchema(): array
    {
        return $this->transform(GeoCoordinateSchemaTransformer::class);
    }

    public function toPoint(): Point
    {
        return new Point(
            (string) $this->latitude,
            (string) $this->longitude,
        );
    }

    protected function validate(): void
    {
        if ($this->latitude < -90 || $this->latitude > 90 || $this->longitude < -180 || $this->longitude > 180) {
            throw new InvalidArgumentException(sprintf(
                'The given latitude "%s" and longitude "%s" values do not represent a valid geographic coordinate.',
                $this->latitude,
                $this->longitude,
            ));
        }
    }
}
