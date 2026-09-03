<?php

namespace Hks\Schema\Data;

use Hks\Schema\Data\Types\GeoCoordinate;
use Hks\Schema\Data\Types\PostalAddress;
use Hks\Schema\TestCase;

class PlaceTest extends TestCase
{
    public function testFromArrayResolvesTheAddressAndCoordinatesFromTheirNativeShape(): void
    {
        $place = Place::fromArray([
            'name' => 'Head Office',
            'address' => ['streetAddress' => 'Musterstraße 1'],
            'coordinates' => ['latitude' => 52.520008, 'longitude' => 13.404954],
        ]);

        $this->assertSame('Head Office', $place->name());
        $this->assertTrue($place->address()->isEqualTo(new PostalAddress(streetAddress: 'Musterstraße 1')));
        $this->assertTrue($place->coordinates()->isEqualTo(new GeoCoordinate(52.520008, 13.404954)));
    }

    public function testFromArrayLeavesOmittedOptionalPropertiesNull(): void
    {
        $place = Place::fromArray(['name' => 'Head Office']);

        $this->assertNull($place->address());
        $this->assertNull($place->coordinates());
    }

    public function testToStructuredDataRendersAPlaceNodeWithAddressAndGeo(): void
    {
        $place = new Place(
            name: 'Head Office',
            address: new PostalAddress(streetAddress: 'Musterstraße 1'),
            coordinates: new GeoCoordinate(52.520008, 13.404954),
        );

        $this->assertSame([
            '@type' => 'Place',
            'name' => 'Head Office',
            'address' => $place->address()->toStructuredData(),
            'geo' => $place->coordinates()->toStructuredData(),
        ], $place->toStructuredData());
    }
}
