<?php

namespace Hks\Schema\Data;

use Kirby\Cms\Structure;
use Kirby\Cms\StructureObject;
use Kirby\Content\Content;
use Spatie\OpeningHours\OpeningHours;

class OpeningHoursFactory
{
    public static function createFromArray(array $data, ?string $timezone = null, ?string $outputTimezone = null): OpeningHours
    {
        return OpeningHours::createAndMergeOverlappingRanges(
            data: $data,
            timezone: $timezone,
            outputTimezone: $outputTimezone,
        );
    }

    public static function createFromContent(Content $structure): OpeningHours
    {
        $timezone = option(
            key: 'hksagentur.schema.hours.timezone',
            default: date_default_timezone_get(),
        );

        $outputTimezone = option(
            key: 'hksagentur.schema.hours.outputTimezone',
            default: $timezone,
        );

        $regularHours = array_map(fn (Structure $timeRanges) => $timeRanges->toArray(fn (StructureObject $timeRange) => [
            'data' => $timeRange->note()->esc(),
            'hours' => sprintf(
                '%s-%s',
                $timeRange->start()->toDate('H:i'),
                $timeRange->end()->toDate('H:i')
            ),
        ]), [
            'monday' => $structure->monday()->toStructure(),
            'tuesday' => $structure->tuesday()->toStructure(),
            'wednesday' => $structure->wednesday()->toStructure(),
            'thursday' => $structure->thursday()->toStructure(),
            'friday' => $structure->friday()->toStructure(),
            'saturday' => $structure->saturday()->toStructure(),
            'sunday' => $structure->sunday()->toStructure(),
        ]);

        $exceptions = $structure->exceptions()->toStructure()->mapWithKeys(fn (StructureObject $timeRange) => [
            $timeRange->date()->toDate('Y-m-d') => [
                'data' => $timeRange->note()->value(),
                'hours' => sprintf(
                    '%s-%s',
                    $timeRange->start()->toDate('H:i'),
                    $timeRange->end()->toDate('H:i')
                ),
            ],
        ])->toArray();

        return static::createFromArray(
            data: $regularHours + ['exceptions' => $exceptions],
            timezone: $timezone,
            outputTimezone: $outputTimezone,
        );
    }
}
