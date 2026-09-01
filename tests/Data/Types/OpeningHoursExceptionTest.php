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
            'data' => ['date' => '2026-12-25', 'note' => 'Closed on Christmas'],
        ]);

        $this->assertSame('00:00:00/00:00:00', $exception->timeRange()->toString());
        $this->assertSame(['date' => '2026-12-25', 'note' => 'Closed on Christmas'], $exception->data());
    }

    public function testFromThrowsGivenAnUnparsableTimeRange(): void
    {
        $this->expectException(ValidationException::class);

        OpeningHoursException::from([
            'timeRange' => ['start' => 'not a time', 'end' => '10:00'],
        ]);
    }

    public function testTryFromReturnsNullGivenAnUnparsableTimeRange(): void
    {
        $this->assertNull(OpeningHoursException::tryFrom([
            'timeRange' => ['start' => 'not a time', 'end' => '10:00'],
        ]));
    }

    public function testToStringRendersAnIsoFormattedTimeSpan(): void
    {
        $exception = OpeningHoursException::from([
            'timeRange' => ['start' => '00:00', 'end' => '00:00'],
            'data' => ['date' => '2026-12-25'],
        ]);

        $this->assertSame('00:00:00/00:00:00', $exception->toString());
    }

    public function testToHtmlRendersADataTagWithBothTimes(): void
    {
        $exception = OpeningHoursException::from([
            'timeRange' => ['start' => '00:00', 'end' => '00:00'],
        ]);

        $this->assertSame(
            "<data value=\"00:00:00/00:00:00\"><time>00:00:00</time>\u{2009}–\u{2009}<time>00:00:00</time></data>",
            $exception->toHtml(),
        );
    }

    public function testToArrayIncludesTheTimeRangeAndData(): void
    {
        $exception = OpeningHoursException::from([
            'timeRange' => ['start' => '00:00', 'end' => '00:00'],
            'data' => ['date' => '2026-12-25'],
        ]);

        $this->assertSame([
            'timeRange' => ['00:00:00', '00:00:00'],
            'data' => ['date' => '2026-12-25'],
        ], $exception->toArray());
    }
}
