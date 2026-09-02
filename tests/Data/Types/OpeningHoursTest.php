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
                    'date' => '2026-12-25',
                    'data' => ['note' => 'Closed on Christmas'],
                ],
            ],
        ]);

        $specifications = $openingHours->specifications();

        $this->assertCount(2, $specifications);

        $weekdaySpec = $specifications[0];
        $this->assertSame(
            [WeekDay::Monday, WeekDay::Tuesday, WeekDay::Wednesday, WeekDay::Thursday, WeekDay::Friday],
            $weekdaySpec->weekDays()->all(),
        );
        $this->assertSame('09:00:00/19:00:00', $weekdaySpec->timeRanges()->toString());

        $saturdaySpec = $specifications[1];
        $this->assertSame([WeekDay::Saturday], $saturdaySpec->weekDays()->all());
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

    private function weeklyOpeningHours(): OpeningHours
    {
        $weekdayHours = [
            ['timespan' => ['start' => '09:00', 'end' => '17:00']],
        ];

        return OpeningHours::fromArray([
            'monday' => $weekdayHours,
            'tuesday' => $weekdayHours,
            'wednesday' => $weekdayHours,
            'thursday' => $weekdayHours,
            'friday' => $weekdayHours,
            'saturday' => [],
            'sunday' => [],
            'exceptions' => [
                [
                    'timeRange' => ['start' => '00:00', 'end' => '00:00'],
                    'date' => '2026-12-25',
                    'data' => ['note' => 'Closed on Christmas'],
                ],
            ],
        ]);
    }

    public function testIsOpenAtReturnsTrueWhenTimeFallsWithinSpecifiedHours(): void
    {
        $openingHours = $this->weeklyOpeningHours();

        // Monday
        $this->assertTrue($openingHours->isOpenAt(new \DateTime('2026-12-21 10:00:00')));
    }

    public function testIsOpenAtReturnsFalseOutsideSpecifiedHours(): void
    {
        $openingHours = $this->weeklyOpeningHours();

        // Monday, after closing time
        $this->assertFalse($openingHours->isOpenAt(new \DateTime('2026-12-21 20:00:00')));
    }

    public function testIsOpenAtReturnsFalseOnADayWithoutASpecification(): void
    {
        $openingHours = $this->weeklyOpeningHours();

        // Sunday
        $this->assertFalse($openingHours->isOpenAt(new \DateTime('2026-12-20 10:00:00')));
    }

    public function testIsOpenAtUsesTheExceptionInsteadOfTheRegularScheduleOnAMatchingDate(): void
    {
        $openingHours = $this->weeklyOpeningHours();

        // Friday, would normally be open at this time
        $this->assertFalse($openingHours->isOpenAt(new \DateTime('2026-12-25 10:00:00')));
    }

    public function testForReturnsTheSpecificationCoveringTheGivenWeekDay(): void
    {
        $openingHours = $this->weeklyOpeningHours();

        $specification = $openingHours->for(WeekDay::Monday);

        $this->assertNotNull($specification);
        $this->assertTrue($specification->weekDays()->contains(WeekDay::Monday));
    }

    public function testForReturnsNullWhenNoSpecificationCoversTheGivenWeekDay(): void
    {
        $openingHours = $this->weeklyOpeningHours();

        $this->assertNull($openingHours->for(WeekDay::Sunday));
    }

    public function testHasExceptionAtMatchesOnlyDatesWithAnException(): void
    {
        $openingHours = $this->weeklyOpeningHours();

        $this->assertTrue($openingHours->hasExceptionAt(new \DateTime('2026-12-25')));
        $this->assertFalse($openingHours->hasExceptionAt(new \DateTime('2026-12-24')));
    }

    public function testHasExceptionDefaultsToNow(): void
    {
        $openingHours = OpeningHours::fromArray([
            'exceptions' => [
                [
                    'timeRange' => ['start' => '00:00', 'end' => '00:00'],
                    'date' => (new \DateTime())->format('Y-m-d'),
                ],
            ],
        ]);

        $this->assertTrue($openingHours->hasException());
    }

    public function testSimplifyMergesSpecificationsThatShareIdenticalTimeRanges(): void
    {
        $openingHours = new OpeningHours(specifications: [
            OpeningHoursSpecification::from([
                'weekDays' => ['Monday'],
                'timeRanges' => [['09:00', '17:00']],
            ]),
            OpeningHoursSpecification::from([
                'weekDays' => ['Tuesday'],
                'timeRanges' => [['09:00', '17:00']],
            ]),
            OpeningHoursSpecification::from([
                'weekDays' => ['Saturday'],
                'timeRanges' => [['08:00', '14:00']],
            ]),
        ]);

        $simplified = $openingHours->simplify();
        $specifications = $simplified->specifications();

        $this->assertCount(2, $specifications);

        $this->assertSame([WeekDay::Monday, WeekDay::Tuesday], $specifications[0]->weekDays()->all());
        $this->assertSame('09:00:00/17:00:00', $specifications[0]->timeRanges()->toString());

        $this->assertSame([WeekDay::Saturday], $specifications[1]->weekDays()->all());
        $this->assertSame('08:00:00/14:00:00', $specifications[1]->timeRanges()->toString());
    }

    public function testSimplifyMergesOverlappingTimeRangesWithinASpecificationBeforeGrouping(): void
    {
        $openingHours = new OpeningHours(specifications: [
            OpeningHoursSpecification::from([
                'weekDays' => ['Monday'],
                'timeRanges' => [['09:00', '12:00'], ['11:00', '17:00']],
            ]),
            OpeningHoursSpecification::from([
                'weekDays' => ['Tuesday'],
                'timeRanges' => [['09:00', '17:00']],
            ]),
        ]);

        $specifications = $openingHours->simplify()->specifications();

        $this->assertCount(1, $specifications);
        $this->assertSame([WeekDay::Monday, WeekDay::Tuesday], $specifications[0]->weekDays()->all());
        $this->assertSame('09:00:00/17:00:00', $specifications[0]->timeRanges()->toString());
    }
}
