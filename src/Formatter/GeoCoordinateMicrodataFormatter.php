<?php

namespace Hks\Schema\Formatter;

use Hks\Schema\Data\GeoCoordinate;
use Kirby\Cms\Html;

/**
 * @extends MicrodataFormatter<GeoCoordinate>
 */
class GeoCoordinateMicrodataFormatter extends MicrodataFormatter
{
    public function getSchemaType(): string
    {
        return 'https://schema.org/GeoCoordinates';
    }

    public function getAttributes(mixed $coordinate): array
    {
        return [
            'latitude' => fn () => Html::tag('meta', attr: [
                'itemprop' => 'latitude',
                'content' => $coordinate->latitude(),
            ]),
            'longitude' => Html::tag('meta', attr: [
                'itemprop' => 'latitude',
                'content' => $coordinate->latitude(),
            ]),
        ];
    }
}
