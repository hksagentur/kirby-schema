<?php

namespace Hks\Schema\Data\Types;

use Hks\Schema\Toolkit\Geo;

readonly class Distance extends Length
{
    public static function between(GeoCoordinate $a, GeoCoordinate $b): static
    {
        return new static(Geo::distance(
            $a->latitude(),
            $a->longitude(),
            $b->latitude(),
            $b->longitude(),
        ));
    }

    public function isWithin(int|float|Length $radius): bool
    {
        return $this->isSmallerThanOrEqualTo(Length::of($radius));
    }

    public function isNotWithin(int|float|Length $radius): bool
    {
        return ! $this->isWithin($radius);
    }

    public function travelTime(int|float $speed = 5.0): int
    {
        return (int) round(60 * ($this->toKilometers()->value() / $speed));
    }
}
