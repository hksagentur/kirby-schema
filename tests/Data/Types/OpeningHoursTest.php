<?php

namespace Hks\Schema\Data\Types;

use Hks\Schema\TestCase;

class OpeningHoursTest extends TestCase
{
    public function testFromArrayGroupsWeekDaysWithIdenticalTimeRanges(): void
    {
        $openingHours = OpeningHours::fromArray([
            'monday' => [
                ['timespan' => ['start' => '09:00', 'end' => '19:00'], 'note' => ''],
            ],
            'tuesday' => [
                ['timespan' => ['start' => '09:00', 'end' => '19:00'], 'note' => ''],
            ],
            'wednesday' => [
                ['timespan' => ['start' => '09:00', 'end' => '19:00'], 'note' => ''],
            ],
            'thursday' => [
                ['timespan' => ['start' => '09:00', 'end' => '19:00'], 'note' => ''],
            ],
            'friday' => [
                ['timespan' => ['start' => '09:00', 'end' => '19:00'], 'note' => ''],
            ],
            'saturday' => [
                ['timespan' => ['start' => '08:00', 'end' => '14:00'], 'note' => ''],
            ],
            'sunday' => [],
            'exceptions' => [
                [
                    'timeRange' => ['start' => '00:00', 'end' => '00:00'],
                    'data' => ['date' => '2026-12-25', 'note' => 'Closed on Christmas'],
                ],
            ],
        ]);

        $specifications = $openingHours->specifications();

        $this->assertCount(2, $specifications);

        $weekdaySpec = $specifications[0];
        $this->assertSame(
            [WeekDay::Monday, WeekDay::Tuesday, WeekDay::Wednesday, WeekDay::Thursday, WeekDay::Friday],
            $weekdaySpec->weekDays()->toArray(),
        );
        $this->assertSame('09:00:00/19:00:00', $weekdaySpec->timeRanges()->toString());

        $saturdaySpec = $specifications[1];
        $this->assertSame([WeekDay::Saturday], $saturdaySpec->weekDays()->toArray());
        $this->assertSame('08:00:00/14:00:00', $saturdaySpec->timeRanges()->toString());

        $this->assertCount(1, $openingHours->exceptions());
    }

    public function testFromArrayIgnoresWeekDaysWithoutEntries(): void
    {
        $openingHours = OpeningHours::fromArray([
            'monday' => [
                ['timespan' => ['start' => '09:00', 'end' => '17:00']],
            ],
        ]);

        $this->assertCount(1, $openingHours->specifications());
        $this->assertSame([], $openingHours->exceptions());
    }
}
