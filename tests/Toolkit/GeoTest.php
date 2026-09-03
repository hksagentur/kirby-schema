<?php

namespace Hks\Schema\Toolkit;

use Hks\Schema\TestCase;

class GeoTest extends TestCase
{
    public function testDistanceCalculatesTheGreatCircleDistanceBetweenTwoCoordinates(): void
    {
        // Berlin -> Hamburg, real-world great-circle distance ~255.2 km.
        $distance = Geo::distance(52.520008, 13.404954, 53.551086, 9.993682);

        $this->assertEqualsWithDelta(255_247, $distance, 1_000);
    }

    public function testDistanceIsZeroForIdenticalCoordinates(): void
    {
        $this->assertSame(0.0, Geo::distance(52.520008, 13.404954, 52.520008, 13.404954));
    }

    public function testFormatLatitudeRendersDegreesMinutesSecondsWithTheCardinalDirection(): void
    {
        $this->assertSame("52°\u{00A0}31'\u{00A0}12.03\"N", Geo::formatLatitude(52.520008));
        $this->assertSame("33°\u{00A0}52'\u{00A0}7.68\"S", Geo::formatLatitude(-33.8688));
    }

    public function testFormatLongitudeRendersDegreesMinutesSecondsWithTheCardinalDirection(): void
    {
        $this->assertSame("13°\u{00A0}24'\u{00A0}17.83\"E", Geo::formatLongitude(13.404954));
        $this->assertSame("70°\u{00A0}38'\u{00A0}53.88\"W", Geo::formatLongitude(-70.6483));
    }
}
