<?php

namespace Hks\Schema\Transformer;

use Hks\Schema\Data\GeoCoordinate;

/**
 * @extends Transformer<GeoCoordinate>
 */
class GeoCoordinateSchemaTransformer extends Transformer
{
    public function transform(object $coordinate): array
    {
        return [
            '@type' => 'GeoCoordinates',
            'latitude' => $coordinate->latitude(),
            'longitude' => $coordinate->longitude(),
        ];
    }
}
