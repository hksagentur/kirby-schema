<?php

namespace Hks\Schema\Integration;

use DateTime;
use Hks\Schema\Data\Types\OpeningHours;
use Hks\Schema\TestCase;
use Kirby\Content\Field;
use Kirby\Data\Yaml;

class OpeningHoursBlueprintResolutionTest extends TestCase
{
    use InteractsWithBlueprintFields;

    public function testToDataTypeResolvesOpeningHoursFromContentShapedByItsOwnBlueprint(): void
    {
        $fields = static::resolvedBlueprintFields('@hksagentur/schema/fields/opening-hours');

        // Each weekday's structure row is shaped by its resolved field
        // names ("start"/"end", flattened from the time-range group), not
        // by a "timespan" key the blueprint doesn't actually produce.
        $weekdayRow = fn (array $row) => array_intersect_key($row, array_flip(array_keys($fields['monday']['fields'])));
        $exceptionRow = fn (array $row) => array_intersect_key($row, array_flip(array_keys($fields['exceptions']['fields'])));

        $regularHours = $weekdayRow(['start' => '09:00', 'end' => '19:00']);

        $content = [
            'monday' => [$regularHours],
            'tuesday' => [$regularHours],
            'wednesday' => [$regularHours],
            'thursday' => [$regularHours],
            'friday' => [$regularHours],
            'saturday' => [$weekdayRow(['start' => '08:00', 'end' => '14:00'])],
            'sunday' => [],
            'exceptions' => [
                $exceptionRow(['date' => '2026-12-24', 'start' => '09:00', 'end' => '12:00', 'note' => 'Christmas Eve']),
            ],
        ];

        $field = new Field(null, 'openingHours', Yaml::encode($content));

        $openingHours = $field->toDataType('opening-hours');

        $this->assertInstanceOf(OpeningHours::class, $openingHours);

        // Monday, within the regular 09:00–19:00 specification.
        $this->assertTrue($openingHours->isOpenAt(new DateTime('2026-01-05 10:00:00')));

        // Saturday, within the shorter 08:00–14:00 specification.
        $this->assertTrue($openingHours->isOpenAt(new DateTime('2026-01-10 09:00:00')));

        // Sunday, no specification at all.
        $this->assertTrue($openingHours->isClosedAllDayAt(new DateTime('2026-01-04 10:00:00')));

        // An exception overrides the (otherwise open) Thursday specification.
        $this->assertTrue($openingHours->hasExceptionAt(new DateTime('2026-12-24')));
        $this->assertTrue($openingHours->isOpenAt(new DateTime('2026-12-24 10:00:00')));
        $this->assertTrue($openingHours->isClosedAt(new DateTime('2026-12-24 13:00:00')));
    }
}
