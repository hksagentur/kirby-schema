<?php

namespace Hks\Schema\Data\Types;

use Hks\Schema\TestCase;

class GeoCoordinateTest extends TestCase
{
    public function testToStructuredDataRendersAGeoCoordinatesNode(): void
    {
        $coordinate = new GeoCoordinate(52.520008, 13.404954);

        $this->assertSame([
            '@type' => 'GeoCoordinates',
            'latitude' => 52.520008,
            'longitude' => 13.404954,
        ], $coordinate->toStructuredData());
    }
}
