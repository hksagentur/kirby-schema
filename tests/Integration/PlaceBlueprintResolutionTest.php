<?php

namespace Hks\Schema\Integration;

use Hks\Schema\Data\Place;
use Hks\Schema\TestCase;
use Kirby\Cms\StructureObject;

class PlaceBlueprintResolutionTest extends TestCase
{
    use InteractsWithBlueprintFields;

    public function testToDataObjectResolvesAPlaceFromContentShapedByItsOwnBlueprint(): void
    {
        $content = static::contentFromBlueprint('@hksagentur/schema/fields/place', [
            'name' => 'Head Office',
            'streetAddress' => 'Musterstraße 1',
            'postalCode' => '12345',
            'addressLocality' => 'Berlin',
            'addressCountry' => 'DE',
            'latitude' => '52.520008',
            'longitude' => '13.404954',
        ]);

        $structureObject = new StructureObject(['content' => $content]);

        $place = $structureObject->toDataObject('place');

        $this->assertInstanceOf(Place::class, $place);
        $this->assertSame('Head Office', $place->name());
        $this->assertSame('Musterstraße 1', $place->address()?->streetAddress());
        $this->assertSame('Berlin', $place->address()?->locality());
        $this->assertSame(52.520008, $place->coordinates()?->latitude());
        $this->assertSame(13.404954, $place->coordinates()?->longitude());
    }
}
