<?php

namespace Hks\Schema\Data;

use Kirby\Geo\Point;
use Kirby\Toolkit\I18n;

class GeoCoordinateFormatter extends Formatter
{
    public function format(Point $point, array $options = []): string
    {
        $locale = $options['locale'] ?? static::defaultLocale();
        $precision = $options['precision'] ?? 3;

        return implode(', ', [
            I18n::formatNumber(round($point->lat(), $precision), $locale),
            I18n::formatNumber(round($point->lng(), $precision), $locale),
        ]);
    }
}
