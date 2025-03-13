<?php

namespace Hks\Schema\Data;

use Kirby\Cms\App;
use Kirby\Geo\Point;
use Kirby\Toolkit\A;
use Kirby\Toolkit\I18n;

class GeoCoordinateFormatter
{
    public function format(Point $point, array $options = []): string
    {
        $options = A::merge([
            'locale' => App::instance()->language()->locale(LC_ALL),
            'precision' => 3,
        ], $options);

        return implode(', ', [
            I18n::formatNumber(round($point->lat(), $options['precision']), $options['locale']),
            I18n::formatNumber(round($point->lng(), $options['precision']), $options['locale']),
        ]);
    }
}
