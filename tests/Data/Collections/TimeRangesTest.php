<?php

namespace Hks\Schema\Data\Collections;

use Hks\Schema\Data\Types\TimeRange;
use Hks\Schema\TestCase;
use Stringable;

class TimeRangesTest extends TestCase
{
    public function testImplementsStringable(): void
    {
        $timeRanges = new TimeRanges([TimeRange::from(['09:00', '17:00'])]);

        $this->assertInstanceOf(Stringable::class, $timeRanges);
        $this->assertSame($timeRanges->toString(), (string) $timeRanges);
    }

    public function testContainsIsTrueWhenAnyTimeRangeContainsTheGivenMoment(): void
    {
        $timeRanges = new TimeRanges([
            TimeRange::from(['09:00', '12:00']),
            TimeRange::from(['13:00', '17:00']),
        ]);

        $this->assertTrue($timeRanges->contains('10:00'));
        $this->assertTrue($timeRanges->contains('15:00'));
    }

    public function testContainsIsFalseWhenNoTimeRangeContainsTheGivenMoment(): void
    {
        $timeRanges = new TimeRanges([
            TimeRange::from(['09:00', '12:00']),
            TimeRange::from(['13:00', '17:00']),
        ]);

        $this->assertFalse($timeRanges->contains('12:30'));
    }

    public function testSimplifyMergesOverlappingRanges(): void
    {
        $timeRanges = new TimeRanges([
            TimeRange::from(['09:00', '12:00']),
            TimeRange::from(['11:00', '14:00']),
        ]);

        $simplified = $timeRanges->simplify();

        $this->assertSame('09:00:00/14:00:00', $simplified->toString());
    }

    public function testSimplifyMergesTouchingRanges(): void
    {
        $timeRanges = new TimeRanges([
            TimeRange::from(['09:00', '12:00']),
            TimeRange::from(['12:00', '17:00']),
        ]);

        $simplified = $timeRanges->simplify();

        $this->assertSame('09:00:00/17:00:00', $simplified->toString());
    }

    public function testSimplifyLeavesSeparateRangesUntouched(): void
    {
        $timeRanges = new TimeRanges([
            TimeRange::from(['09:00', '12:00']),
            TimeRange::from(['13:00', '17:00']),
        ]);

        $simplified = $timeRanges->simplify();

        $this->assertSame('09:00:00/12:00:00, 13:00:00/17:00:00', $simplified->toString());
    }

    public function testSimplifyKeepsMergingAgainstTheAccumulatedRangeNotJustTheOriginalNeighbour(): void
    {
        // The middle range is fully contained within the first; comparing the
        // next range only against the immediately preceding *original* range
        // (rather than the still-growing merged range) would miss that the
        // merged range also reaches into the third one.
        $timeRanges = new TimeRanges([
            TimeRange::from(['09:00', '13:00']),
            TimeRange::from(['10:00', '11:00']),
            TimeRange::from(['12:30', '14:00']),
        ]);

        $this->assertSame('09:00:00/14:00:00', $timeRanges->simplify()->toString());
    }

    public function testSimplifyOnAnEmptyCollectionReturnsAnEmptyCollection(): void
    {
        $this->assertTrue((new TimeRanges([]))->simplify()->isEmpty());
    }

    public function testMapPassesTheKeyAlongsideTheValueAndPreservesKeysInTheResult(): void
    {
        $timeRanges = new TimeRanges([
            TimeRange::from(['09:00', '12:00']),
            TimeRange::from(['13:00', '17:00']),
        ]);

        $mapped = $timeRanges->map(fn (TimeRange $timeRange, int $key) => "{$key}:{$timeRange->toString()}");

        $this->assertSame([
            0 => '0:09:00:00/12:00:00',
            1 => '1:13:00:00/17:00:00',
        ], $mapped);
    }

    public function testAllReturnsTheRawTimeRangeInstances(): void
    {
        $range = TimeRange::from(['09:00', '12:00']);
        $timeRanges = new TimeRanges([$range]);

        $this->assertSame([$range], $timeRanges->all());
    }

    public function testToArrayReturnsEachTimeRangesOwnArrayRepresentation(): void
    {
        $timeRanges = new TimeRanges([
            TimeRange::from(['09:00', '12:00']),
            TimeRange::from(['13:00', '17:00']),
        ]);

        $this->assertSame([
            ['09:00:00', '12:00:00'],
            ['13:00:00', '17:00:00'],
        ], $timeRanges->toArray());
    }

    public function testAtReturnsTheTimeRangeContainingTheGivenMoment(): void
    {
        $timeRanges = new TimeRanges([
            TimeRange::from(['09:00', '12:00']),
            TimeRange::from(['13:00', '17:00']),
        ]);

        $this->assertSame('17:00:00', $timeRanges->at('15:00')?->end()->toString());
        $this->assertNull($timeRanges->at('12:30'));
    }

    public function testToLocaleStringFormatsAllTimeRanges(): void
    {
        $timeRanges = new TimeRanges([
            TimeRange::from(['09:00', '12:00']),
            TimeRange::from(['13:00', '17:00']),
        ]);

        $this->assertSame($timeRanges->format(locale: 'de'), $timeRanges->toLocaleString('de'));
    }
}
