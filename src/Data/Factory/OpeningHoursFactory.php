<?php

namespace Hks\Schema\Data\Factory;

use Kirby\Cms\Structure;
use Kirby\Cms\StructureObject;
use Kirby\Content\Content;
use Spatie\OpeningHours\OpeningHours;

class OpeningHoursFactory
{
    public static function createFromArray(array $attributes): OpeningHours
    {
        return OpeningHours::createAndMergeOverlappingRanges($attributes);
    }

    public static function createFromContent(Content $structure): OpeningHours
    {
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

        return OpeningHours::createAndMergeOverlappingRanges($regularHours + ['exceptions' => $exceptions]);
    }
}
