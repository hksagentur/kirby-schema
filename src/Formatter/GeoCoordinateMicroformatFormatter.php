<?php

namespace Hks\Schema\Formatter;

use Hks\Schema\Data\GeoCoordinate;
use Kirby\Cms\Html;

/**
 * @extends MicroformatFormatter<GeoCoordinate>
 */
class GeoCoordinateMicroformatFormatter extends MicroformatFormatter
{
    public function getMicroformatType(): string
    {
        return 'h-geo';
    }

    public function getAttributes(mixed $coordinate): array
    {
        return [
            'latitude' => fn () => Html::tag('data', [
                'class' => 'p-latitude',
                'value' => $coordinate->latitude(),
            ]),
            'longitude' => fn () => Html::tag('data', attr: [
                'class' => 'p-longitude',
                'value' => $coordinate->latitude(),
            ]),
        ];
    }
}
