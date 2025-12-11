<?php

namespace Hks\Schema\Formatter;

use Hks\Schema\Data\GeoCoordinate;
use Kirby\Toolkit\A;

/**
 * @extends Formatter<GeoCoordinate>
 */
class GeoCoordinateHtmlFormatter extends Formatter
{
    public function defaults(): array
    {
        return [
            'format' => 'microformat',
        ];
    }

    public function format(mixed $coordinate): string
    {
        $options = A::without($this->options(), ['format']);

        $formatter = match ($this->option('format')) {
            'microdata' => new GeoCoordinateMicrodataFormatter($options),
            default => new GeoCoordinateMicroformatFormatter($options),
        };

        return $formatter->format($coordinate);
    }
}
