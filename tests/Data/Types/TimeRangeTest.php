<?php

namespace Hks\Schema\Data\Types;

use Hks\Schema\Data\Exceptions\ValidationException;
use Hks\Schema\TestCase;

class TimeRangeTest extends TestCase
{
    public function testFromCreatesANewInstanceFromStartAndEndKeys(): void
    {
        $range = TimeRange::from(['start' => '09:00', 'end' => '17:00']);

        $this->assertTrue($range->start()->isEqualTo(Time::from('09:00')));
        $this->assertTrue($range->end()->isEqualTo(Time::from('17:00')));
    }

    public function testToStringOutputRoundTripsThroughFrom(): void
    {
        $range = TimeRange::from(['09:00', '17:30']);

        $this->assertSame($range->toString(), TimeRange::from($range->toString())->toString());
    }

    public function testFromThrowsGivenAnIncompleteArray(): void
    {
        $this->expectException(ValidationException::class);

        TimeRange::from(['09:00']);
    }

    public function testTryFromReturnsNullGivenAnIncompleteArray(): void
    {
        $this->assertNull(TimeRange::tryFrom(['09:00']));
    }

    public function testToHtmlRendersADataTagWrappingBothTimes(): void
    {
        $this->assertSame(
            "<data value=\"09:00:00/17:30:00\"><time>09:00:00</time>\u{2009}–\u{2009}<time>17:30:00</time></data>",
            TimeRange::from(['09:00', '17:30'])->toHtml(),
        );
    }

    public function testContainsChecksWhetherATimeFallsWithinTheRange(): void
    {
        $range = TimeRange::from(['09:00', '17:00']);

        $this->assertTrue($range->contains('12:00'));
        $this->assertFalse($range->contains('18:00'));
    }

    public function testIsOverNightDetectsAnEndTimeBeforeTheStartTime(): void
    {
        $this->assertTrue(TimeRange::from(['22:00', '06:00'])->isOverNight());
        $this->assertFalse(TimeRange::from(['09:00', '17:00'])->isOverNight());
    }

    public function testCompareOrdersByStartTime(): void
    {
        $earlier = TimeRange::from(['08:00', '12:00']);
        $later = TimeRange::from(['09:00', '17:00']);

        $this->assertTrue($earlier->compare($later) < 0);
    }
}
