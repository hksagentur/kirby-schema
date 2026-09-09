<?php

namespace Hks\Schema\Data\Types;

use Hks\Schema\Data\Contracts\Schemable;
use Hks\Schema\TestCase;
use Kirby\Cms\Blueprint;

class OpeningHoursTest extends TestCase
{
    public function testImplementsSchemable(): void
    {
        $this->assertInstanceOf(Schemable::class, new OpeningHours(specifications: []));
    }

    public function testResolvedBlueprintFieldsProduceTheFlatShapeFromArrayExpects(): void
    {
        // Every sub-field involved (`opening-hours-specification`'s `timeRange`,
        // `opening-hours-exception`'s date/start/end/note) is a `type: group` or
        // otherwise flattened by Kirby's blueprint loader before content is ever
        // saved — resolve the real blueprint rather than trusting the YAML source,
        // per AGENTS.md's "Composite blueprints" section.
        $fields = Blueprint::fieldsProps([
            'openingHours' => ['extends' => '@hksagentur/schema/fields/opening-hours'],
        ]);

        $monday = $fields['openingHours']['fields']['monday'];
        $this->assertSame('structure', $monday['type']);
        $this->assertSame(['start', 'end'], array_keys($monday['fields']));

        $exceptions = $fields['openingHours']['fields']['exceptions'];
        $this->assertSame('structure', $exceptions['type']);
        $this->assertSame(['date', 'start', 'end', 'note'], array_keys($exceptions['fields']));
    }

    public function testFromArrayGroupsWeekDaysWithIdenticalTimeRanges(): void
    {
        $openingHours = OpeningHours::fromArray([
            'monday' => [
                ['start' => '09:00', 'end' => '19:00', 'note' => ''],
            ],
            'tuesday' => [
                ['start' => '09:00', 'end' => '19:00', 'note' => ''],
            ],
            'wednesday' => [
                ['start' => '09:00', 'end' => '19:00', 'note' => ''],
            ],
            'thursday' => [
                ['start' => '09:00', 'end' => '19:00', 'note' => ''],
            ],
            'friday' => [
                ['start' => '09:00', 'end' => '19:00', 'note' => ''],
            ],
            'saturday' => [
                ['start' => '08:00', 'end' => '14:00', 'note' => ''],
            ],
            'sunday' => [],
            'exceptions' => [
                [
                    'timeRange' => ['start' => '00:00', 'end' => '00:00'],
                    'date' => '2026-12-25',
                    'note' => 'Closed on Christmas',
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
                ['start' => '09:00', 'end' => '17:00'],
            ],
        ]);

        $this->assertCount(1, $openingHours->specifications());
        $this->assertSame([], $openingHours->exceptions());
    }

    public function testFromArrayNeverLetsOneMalformedExceptionDiscardTheWholeObject(): void
    {
        $openingHours = OpeningHours::fromArray([
            'monday' => [
                ['start' => '09:00', 'end' => '17:00'],
            ],
            'exceptions' => [
                // Missing its date — invalid on its own.
                ['start' => '00:00', 'end' => '00:00'],
                ['date' => '2026-12-25', 'start' => '00:00', 'end' => '00:00'],
            ],
        ]);

        $this->assertCount(1, $openingHours->specifications());
        $this->assertCount(1, $openingHours->exceptions());
        $this->assertSame('2026-12-25', $openingHours->exceptions()[0]->date()->format('Y-m-d'));
    }

    public function testFromArrayResolvesAClosedAllDayExceptionFromTheFlatBlueprintShape(): void
    {
        $openingHours = OpeningHours::fromArray([
            'monday' => [
                ['start' => '09:00', 'end' => '17:00'],
            ],
            'exceptions' => [
                ['date' => '2026-12-25', 'start' => '', 'end' => '', 'note' => 'Public holiday'],
            ],
        ]);

        // Friday, would normally be open at this time.
        $this->assertFalse($openingHours->isOpenAt(new \DateTime('2026-12-25 10:00:00')));
    }

    public function testIsClosedAllDayAtIsTrueForAClosedAllDayException(): void
    {
        $openingHours = OpeningHours::fromArray([
            'monday' => [
                ['start' => '09:00', 'end' => '17:00'],
            ],
            'exceptions' => [
                ['date' => '2026-12-25', 'start' => '', 'end' => ''],
                ['date' => '2026-12-24', 'start' => '10:00', 'end' => '14:00'],
            ],
        ]);

        $this->assertTrue($openingHours->isClosedAllDayAt(new \DateTime('2026-12-25')));
        // Has an exception, but one with its own time range — a modified
        // schedule, not a full-day closure.
        $this->assertFalse($openingHours->isClosedAllDayAt(new \DateTime('2026-12-24')));
        // Monday itself has hours — being asked about right now vs. "all day"
        // are different questions; isClosedAllDayAt() only answers the latter.
        $this->assertFalse($openingHours->isClosedAllDayAt(new \DateTime('2026-12-21 22:00:00')));
    }

    public function testIsClosedAllDayAtIsTrueForAWeekDayWithoutAnySpecification(): void
    {
        $openingHours = OpeningHours::fromArray([
            'monday' => [
                ['start' => '09:00', 'end' => '17:00'],
            ],
        ]);

        // Sunday has no specification and no exception — closed the entire day.
        $this->assertTrue($openingHours->isClosedAllDayAt(new \DateTime('2026-12-20')));
    }

    private function weeklyOpeningHours(): OpeningHours
    {
        $weekdayHours = [
            ['start' => '09:00', 'end' => '17:00'],
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
                    'note' => 'Closed on Christmas',
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

    public function testHasSpecificationAtMatchesOnlyWeekDaysWithASpecification(): void
    {
        $openingHours = $this->weeklyOpeningHours();

        $this->assertTrue($openingHours->hasSpecificationAt(WeekDay::Monday));
        $this->assertFalse($openingHours->hasSpecificationAt(WeekDay::Sunday));
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

    public function testToStructuredDataFlattensSpecificationsAndAppendsExceptions(): void
    {
        $openingHours = OpeningHours::from([
            'monday' => [['start' => '09:00', 'end' => '17:00']],
            'exceptions' => [
                ['date' => '2026-12-25', 'start' => '00:00', 'end' => '00:00'],
            ],
        ]);

        $this->assertSame([
            [
                '@type' => 'OpeningHoursSpecification',
                'dayOfWeek' => ['https://schema.org/Monday'],
                'opens' => '09:00:00',
                'closes' => '17:00:00',
            ],
            [
                '@type' => 'OpeningHoursSpecification',
                'opens' => '00:00:00',
                'closes' => '00:00:00',
                'validFrom' => '2026-12-25',
                'validThrough' => '2026-12-25',
            ],
        ], $openingHours->toStructuredData());
    }
}
