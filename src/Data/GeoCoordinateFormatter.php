<?php

namespace Hks\Schema\Data;

use Kirby\Geo\Point;
use Kirby\Toolkit\A;

class GeoCoordinateFormatter
{
    public function format(Point $point, array $options = []): string
    {
        $options = A::merge([
            'precision' => 3,
        ], $options);

        return implode(', ', [
            round($point->lat(), $options['precision']),
            round($point->lng(), $options['precision']),
        ]);
    }
}
