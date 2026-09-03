<?php

namespace Hks\Schema\Data\Types;

use Hks\Schema\TestCase;

class DistanceTest extends TestCase
{
    public function testBetweenCalculatesTheDistanceBetweenTwoCoordinates(): void
    {
        $berlin = new GeoCoordinate(52.520008, 13.404954);
        $hamburg = new GeoCoordinate(53.551086, 9.993682);

        $distance = Distance::between($berlin, $hamburg);

        $this->assertEqualsWithDelta(255_000, $distance->toMeters()->value(), 5_000);
    }

    public function testIsWithinAcceptsAPlainNumberOfMeters(): void
    {
        $distance = new Distance(500);

        $this->assertTrue($distance->isWithin(1_000));
        $this->assertFalse($distance->isWithin(100));
    }

    public function testIsWithinAcceptsALengthInADifferentUnit(): void
    {
        $distance = new Distance(500);

        $this->assertTrue($distance->isWithin(new Length(1, LengthUnit::Kilometer)));
        $this->assertFalse($distance->isWithin(new Length(100, LengthUnit::Meter)));
    }

    public function testIsNotWithinIsTheInverseOfIsWithin(): void
    {
        $distance = new Distance(500);

        $this->assertTrue($distance->isNotWithin(100));
        $this->assertFalse($distance->isNotWithin(1_000));
    }

    public function testTravelTimeEstimatesMinutesAtTheGivenSpeed(): void
    {
        $distance = new Distance(5_000);

        $this->assertSame(60, $distance->travelTime(5.0));
        $this->assertSame(5, $distance->travelTime(60.0));
    }
}
