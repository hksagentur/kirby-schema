<?php

namespace Hks\Schema\Data;

use Kirby\Geo\Point;
use Kirby\Content\Content;

class GeoCoordinateFactory
{
    public static function createFromArray(array $data): Point
    {
        return new Point(
            lat: (float) $data['latitude'] ?? $data['lat'] ?? $data[0],
            lng: (float) $data['longitude'] ?? $data['lng'] ?? $data[1],
        );
    }

    public static function createFromContent(Content $content): Point
    {
        return new Point(
            lat: $content->latitude()->or($content->lat())->toFloat(),
            lng: $content->longitude()->or($content->lng())->toFloat(),
        );
    }
}
