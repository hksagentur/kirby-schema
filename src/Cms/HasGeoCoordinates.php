<?php

namespace Hks\Schema\Cms;

use Hks\Schema\Data\GeoCoordinateFactory;
use Kirby\Geo\Geo;
use Kirby\Geo\Point;

trait HasGeoCoordinates
{
    protected ?Point $coordinates = null;

    public function geoCoordinates(): Point
    {
        return $this->coordinates ??= GeoCoordinateFactory::createFromContent($this->content());
    }

    public function latitude(): float
    {
        return $this->geoCoordinates()->lat();
    }

    public function longitude(): float
    {
        return $this->geoCoordinates()->lng();
    }

    public function distanceTo(self|Point $target, string $unit = 'km'): float
    {
        $origin = $this->geoCoordinates();

        if ($target instanceof self) {
            $target = $target->geoCoordinates();
        }

        return Geo::distance($origin, $target, $unit);
    }
}
