<?php

namespace Hks\Schema\Data\Types;

use Hks\Schema\Data\Exceptions\ValidationException;
use Hks\Schema\TestCase;

class TimeTest extends TestCase
{
    public function testFromCreatesANewInstanceFromAnIsoTimeString(): void
    {
        $time = Time::from('09:05:30');

        $this->assertSame([9, 5, 30], [$time->hour(), $time->minute(), $time->second()]);
        $this->assertSame('09:05:30', $time->toString());
    }

    public function testFromDefaultsMissingSecondsToZero(): void
    {
        $this->assertSame(0, Time::from('09:05')->second());
    }

    public function testFromThrowsGivenAnUnparsableString(): void
    {
        $this->expectException(ValidationException::class);

        Time::from('not a time');
    }

    public function testFromThrowsGivenAnOutOfRangeValue(): void
    {
        $this->expectException(ValidationException::class);

        Time::from(['hour' => 25]);
    }

    public function testConstructorAcceptsTheLatestValidTimeOfDay(): void
    {
        $this->assertSame('23:59:59', (new Time(23, 59, 59))->toString());
    }

    public function testTryFromReturnsNullGivenInvalidInput(): void
    {
        $this->assertNull(Time::tryFrom('not a time'));
    }

    public function testToStringReturnsIsoFormattedTime(): void
    {
        $this->assertSame('09:05:30', Time::from('09:05:30')->toString());
    }

    public function testToHtmlRendersATimeTag(): void
    {
        $this->assertSame('<time>09:05:30</time>', Time::from('09:05:30')->toHtml());
    }

    public function testCompareOrdersByTimeOfDay(): void
    {
        $this->assertTrue(Time::from('08:00')->isBefore(Time::from('09:00')));
        $this->assertTrue(Time::from('09:00')->isAfter(Time::from('08:00')));
        $this->assertTrue(Time::from('09:00')->isEqualTo(Time::from('09:00')));
    }

    public function testMinReturnsTheEarlierOfBothTimes(): void
    {
        $earlier = Time::from('08:00');
        $later = Time::from('09:00');

        $this->assertSame($earlier, $earlier->min($later));
        $this->assertSame($earlier, $later->min($earlier));
    }

    public function testMaxReturnsTheLaterOfBothTimes(): void
    {
        $earlier = Time::from('08:00');
        $later = Time::from('09:00');

        $this->assertSame($later, $earlier->max($later));
        $this->assertSame($later, $later->max($earlier));
    }
}
