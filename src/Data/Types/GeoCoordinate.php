<?php

namespace Hks\Schema\Data\Types;

use Hks\Schema\Data\Attributes\ArrayInput;
use Hks\Schema\Data\Contracts\Arrayable;
use Hks\Schema\Data\Contracts\Htmlable;
use Hks\Schema\Data\Contracts\Localizable;
use Hks\Schema\Data\Contracts\Schemable;
use Hks\Schema\Toolkit\Geo;
use InvalidArgumentException;
use Kirby\Toolkit\Html;
use Kirby\Toolkit\Str;
use Stringable;
use Throwable;

#[ArrayInput]
readonly class GeoCoordinate extends DataType implements Arrayable, Htmlable, Localizable, Schemable
{
    public function __construct(
        public float $latitude,
        public float $longitude,
    ) {
        if ($latitude < -90 || $latitude > 90 || $longitude < -180 || $longitude > 180) {
            throw new InvalidArgumentException(sprintf(
                'The given latitude "%s" and longitude "%s" values do not represent a valid geographic coordinate.',
                $latitude,
                $longitude,
            ));
        }
    }

    public static function from(string|array|self|Stringable $value): static
    {
        if ($value instanceof self) {
            return $value;
        }

        if (is_array($value)) {
            $latitude = $value['latitude'] ?? $value['lat'] ?? $value[0] ?? null;
            $longitude = $value['longitude'] ?? $value['lng'] ?? $value[1] ?? null;

            if ($latitude === null || $longitude === null) {
                throw new InvalidArgumentException(
                    'The given array does not represent a valid geographic coordinate.'
                );
            }

            return new static((float) $latitude, (float) $longitude);
        }

        $value = (string) $value;

        if (! Str::contains($value, ',')) {
            throw new InvalidArgumentException(sprintf(
                'The given string "%s" does not represent a valid geographic coordinate.',
                $value,
            ));
        }

        [$latitude, $longitude] = Str::split((string) $value, ',');

        return new static(
            (float) $latitude,
            (float) $longitude,
        );
    }

    public static function tryFrom(string|array|self|Stringable $value): ?static
    {
        try {
            return static::from($value);
        } catch (Throwable) {
            return null;
        }
    }

    public function isWithin(self $origin, int|float|Length $radius): bool
    {
        return $this->distanceTo($origin)->isWithin($radius);
    }

    public function isNotWithin(self $origin, int|float|Length $radius): bool
    {
        return ! $this->isWithin($origin, $radius);
    }

    public function latitude(): float
    {
        return $this->latitude;
    }

    public function longitude(): float
    {
        return $this->longitude;
    }

    public function distanceTo(self $other): Distance
    {
        return Distance::between($this, $other);
    }

    public function travelTime(self $origin, int|float $speed = 5.0): int
    {
        return $this->distanceTo($origin)->travelTime($speed);
    }

    public function toArray(): array
    {
        return [
            'latitude' => $this->latitude(),
            'longitude' => $this->longitude(),
        ];
    }

    public function toStructuredData(): array
    {
        return [
            '@type' => 'GeoCoordinates',
            ...$this->toArray(),
        ];
    }

    public function toUri(): string
    {
        return sprintf(
            'geo:%f,%f',
            $this->latitude(),
            $this->longitude(),
        );
    }

    public function toString(): string
    {
        return sprintf(
            '%f, %f',
            $this->latitude(),
            $this->longitude(),
        );
    }

    public function toLocaleString(?string $locale = null): string
    {
        return sprintf(
            '%s, %s',
            Geo::formatLatitude($this->latitude(), $locale),
            Geo::formatLongitude($this->longitude(), $locale),
        );
    }

    public function toHtml(array $attributes = []): string
    {
        return Html::tag('div', [
            Html::tag('data', Geo::formatLatitude($this->latitude()), [
                'value' => $this->latitude(),
                'itemprop' => 'latitude',
            ]),
            Html::tag('data', Geo::formatLongitude($this->longitude()), [
                'value' => $this->longitude(),
                'itemprop' => 'longitude',
            ]),
        ], [
            'itemscope' => true,
            'itemtype' => 'https://schema.org/GeoCoordinates',
            ...$attributes,
        ]);
    }

    public function toLink(array $attributes): string
    {
        return Html::link($this->toUri(), [$this->toHtml()], $attributes);
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
