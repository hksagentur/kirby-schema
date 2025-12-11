<?php

namespace Hks\Schema\Data\Factory;

use Hks\Schema\Data\GeoCoordinate;
use Kirby\Content\Content;

class GeoCoordinateFactory
{
    public static function createFromArray(array $attributes): GeoCoordinate
    {
        return GeoCoordinate::fromArray($attributes);
    }

    public static function createFromContent(Content $content): GeoCoordinate
    {
        return GeoCoordinate::fromArray(array_filter([
            'latitude' => $content->latitude()->or($content->lat())->toFloat(),
            'longitude' => $content->longitude()->or($content->lng())->toFloat(),
        ]));
    }
}
