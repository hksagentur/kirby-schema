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
            'data' => ['note' => 'Closed on Christmas'],
        ]);

        $this->assertSame('00:00:00/00:00:00', $exception->timeRange()->toString());
        $this->assertSame('2026-12-25', $exception->date()->format('Y-m-d'));
        $this->assertSame(['note' => 'Closed on Christmas'], $exception->data());
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

    public function testDataReturnsASingleValueByKeyOrTheWholeArrayWhenNoKeyIsGiven(): void
    {
        $exception = OpeningHoursException::from([
            'timeRange' => ['start' => '00:00', 'end' => '00:00'],
            'date' => '2026-12-25',
            'data' => ['note' => 'Closed on Christmas'],
        ]);

        $this->assertSame('Closed on Christmas', $exception->data('note'));
        $this->assertNull($exception->data('missing'));
        $this->assertSame(['note' => 'Closed on Christmas'], $exception->data());
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

    public function testToArrayIncludesTheTimeRangeDateAndData(): void
    {
        $exception = OpeningHoursException::from([
            'timeRange' => ['start' => '00:00', 'end' => '00:00'],
            'date' => '2026-12-25',
            'data' => ['note' => 'Closed on Christmas'],
        ]);

        $this->assertSame([
            'timeRange' => ['00:00:00', '00:00:00'],
            'date' => '2026-12-25',
            'data' => ['note' => 'Closed on Christmas'],
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
}
