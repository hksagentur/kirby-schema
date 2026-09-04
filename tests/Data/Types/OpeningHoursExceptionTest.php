<?php

namespace Hks\Schema\Data\Types;

use Hks\Schema\Data\Exceptions\ValidationException;
use Hks\Schema\TestCase;

class OpeningHoursExceptionTest extends TestCase
{
    public function testFromCreatesANewInstanceFromRawArrayDefinitions(): void
    {
        $exception = OpeningHoursException::from([
            'timeRange' => ['start' => '00:00', 'end' => '00:00'],
            'date' => '2026-12-25',
            'note' => 'Closed on Christmas',
        ]);

        $this->assertSame('00:00:00/00:00:00', $exception->timeRange()->toString());
        $this->assertSame('2026-12-25', $exception->date()->format('Y-m-d'));
        $this->assertSame('Closed on Christmas', $exception->note());
    }

    public function testFromThrowsGivenNoDate(): void
    {
        $this->expectException(ValidationException::class);

        OpeningHoursException::from([
            'timeRange' => ['start' => '00:00', 'end' => '00:00'],
        ]);
    }

    public function testFromThrowsGivenAnUnparsableTimeRange(): void
    {
        $this->expectException(ValidationException::class);

        OpeningHoursException::from([
            'timeRange' => ['start' => 'not a time', 'end' => '10:00'],
            'date' => '2026-12-25',
        ]);
    }

    public function testTryFromReturnsNullGivenAnUnparsableTimeRange(): void
    {
        $this->assertNull(OpeningHoursException::tryFrom([
            'timeRange' => ['start' => 'not a time', 'end' => '10:00'],
            'date' => '2026-12-25',
        ]));
    }

    public function testNoteReturnsNullWhenNoneIsGiven(): void
    {
        $exception = OpeningHoursException::from([
            'timeRange' => ['start' => '00:00', 'end' => '00:00'],
            'date' => '2026-12-25',
        ]);

        $this->assertNull($exception->note());
    }

    public function testContainsMatchesOnlyTheExceptionsOwnCalendarDate(): void
    {
        $exception = OpeningHoursException::from([
            'timeRange' => ['start' => '00:00', 'end' => '00:00'],
            'date' => '2026-12-25',
        ]);

        $this->assertTrue($exception->contains(new \DateTime('2026-12-25 10:00:00')));
        $this->assertFalse($exception->contains(new \DateTime('2026-12-26 10:00:00')));
    }

    public function testToStringRendersAnIsoFormattedTimeSpan(): void
    {
        $exception = OpeningHoursException::from([
            'timeRange' => ['start' => '00:00', 'end' => '00:00'],
            'date' => '2026-12-25',
        ]);

        $this->assertSame('00:00:00/00:00:00', $exception->toString());
    }

    public function testToHtmlRendersADataTagWithBothTimes(): void
    {
        $exception = OpeningHoursException::from([
            'timeRange' => ['start' => '00:00', 'end' => '00:00'],
            'date' => '2026-12-25',
        ]);

        $this->assertSame(
            "<data value=\"00:00:00/00:00:00\"><time>00:00:00</time>\u{2009}–\u{2009}<time>00:00:00</time></data>",
            $exception->toHtml(),
        );
    }

    public function testToArrayIncludesTheTimeRangeDateAndNote(): void
    {
        $exception = OpeningHoursException::from([
            'timeRange' => ['start' => '00:00', 'end' => '00:00'],
            'date' => '2026-12-25',
            'note' => 'Closed on Christmas',
        ]);

        $this->assertSame([
            'timeRange' => ['00:00:00', '00:00:00'],
            'date' => '2026-12-25',
            'note' => 'Closed on Christmas',
        ], $exception->toArray());
    }

    public function testToStructuredDataRendersAnOpeningHoursSpecificationValidOnlyForItsOwnDate(): void
    {
        $exception = OpeningHoursException::from([
            'timeRange' => ['start' => '00:00', 'end' => '00:00'],
            'date' => '2026-12-25',
        ]);

        $this->assertSame([
            '@type' => 'OpeningHoursSpecification',
            'opens' => '00:00:00',
            'closes' => '00:00:00',
            'validFrom' => '2026-12-25',
            'validThrough' => '2026-12-25',
        ], $exception->toStructuredData());
    }

    public function testFromAcceptsTheFlatDateStartEndAndNoteKeysAResolvedBlueprintRowProduces(): void
    {
        // `opening-hours-exception.yml` is a `structure` field whose `time-range`
        // group flattens fully into the row — there's never a nested "timeRange"
        // key in real panel content, only flat `date`/`start`/`end`/`note`.
        $exception = OpeningHoursException::from([
            'date' => '2026-12-25',
            'start' => '10:00',
            'end' => '14:00',
            'note' => 'Shortened hours',
        ]);

        $this->assertSame('2026-12-25', $exception->date()->format('Y-m-d'));
        $this->assertSame('10:00:00/14:00:00', $exception->timeRange()->toString());
        $this->assertSame('Shortened hours', $exception->note());
    }

    public function testFromTreatsAMissingStartAndEndAsClosedAllDay(): void
    {
        $exception = OpeningHoursException::from([
            'date' => '2026-12-25',
            'start' => '',
            'end' => '',
            'note' => 'Public holiday',
        ]);

        $this->assertNull($exception->timeRange());
        $this->assertSame('Public holiday', $exception->note());
    }

    public function testFromThrowsGivenAnEmptyDate(): void
    {
        $this->expectException(ValidationException::class);

        OpeningHoursException::from([
            'date' => '',
            'start' => '10:00',
            'end' => '14:00',
        ]);
    }

    public function testToStringRendersClosedWhenThereIsNoTimeRange(): void
    {
        $exception = OpeningHoursException::from([
            'date' => '2026-12-25',
            'start' => '',
            'end' => '',
        ]);

        $this->assertSame('closed', $exception->toString());
    }

    public function testToHtmlRendersTheClosedLabelWhenThereIsNoTimeRange(): void
    {
        $exception = OpeningHoursException::from([
            'date' => '2026-12-25',
            'start' => '',
            'end' => '',
        ]);

        $this->assertStringContainsString('Closed', $exception->toHtml());
    }

    public function testToArrayRepresentsAClosedExceptionWithANullTimeRange(): void
    {
        $exception = OpeningHoursException::from([
            'date' => '2026-12-25',
            'start' => '',
            'end' => '',
        ]);

        $this->assertSame([
            'timeRange' => null,
            'date' => '2026-12-25',
            'note' => null,
        ], $exception->toArray());
    }

    public function testToStructuredDataOmitsOpensAndClosesWhenClosedAllDay(): void
    {
        $exception = OpeningHoursException::from([
            'date' => '2026-12-25',
            'start' => '',
            'end' => '',
        ]);

        $this->assertSame([
            '@type' => 'OpeningHoursSpecification',
            'validFrom' => '2026-12-25',
            'validThrough' => '2026-12-25',
        ], $exception->toStructuredData());
    }
}
