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

    public function testFromResolvesLatAndLngShorthandKeys(): void
    {
        $coordinate = GeoCoordinate::from(['lat' => 52.520008, 'lng' => 13.404954]);

        $this->assertSame(52.520008, $coordinate->latitude());
        $this->assertSame(13.404954, $coordinate->longitude());
    }

    public function testFromResolvesAnIndexedArrayShape(): void
    {
        $coordinate = GeoCoordinate::from([52.520008, 13.404954]);

        $this->assertSame(52.520008, $coordinate->latitude());
        $this->assertSame(13.404954, $coordinate->longitude());
    }

    public function testTryFromReturnsNullInsteadOfANullIslandWhenCoordinatesAreMissing(): void
    {
        $this->assertNull(GeoCoordinate::tryFrom(['name' => 'Acme Inc.']));
    }

    public function testIsWithinChecksTheDistanceToAnotherCoordinateAgainstARadius(): void
    {
        $berlin = new GeoCoordinate(52.520008, 13.404954);
        $hamburg = new GeoCoordinate(53.551086, 9.993682);

        $this->assertTrue($berlin->isWithin($hamburg, 300_000));
        $this->assertFalse($berlin->isWithin($hamburg, 100_000));
    }

    public function testIsNotWithinIsTheInverseOfIsWithin(): void
    {
        $berlin = new GeoCoordinate(52.520008, 13.404954);
        $hamburg = new GeoCoordinate(53.551086, 9.993682);

        $this->assertTrue($berlin->isNotWithin($hamburg, 100_000));
        $this->assertFalse($berlin->isNotWithin($hamburg, 300_000));
    }

    public function testTravelTimeEstimatesMinutesToAnotherCoordinateAtTheGivenSpeed(): void
    {
        $berlin = new GeoCoordinate(52.520008, 13.404954);
        $hamburg = new GeoCoordinate(53.551086, 9.993682);

        $this->assertEqualsWithDelta(255, $berlin->travelTime($hamburg, 60.0), 6);
    }
}
