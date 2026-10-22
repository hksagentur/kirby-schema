<?php

namespace Hks\Schema\Data\Collections;

use Hks\Schema\Data\Types\WeekDay;
use Hks\Schema\TestCase;

class WeekDaysTest extends TestCase
{
    public function testJoinRendersARangeFromFirstToLastDay(): void
    {
        $weekDays = new WeekDays([WeekDay::Monday, WeekDay::Tuesday, WeekDay::Wednesday]);

        $this->assertSame(
            '<abbr>Mo</abbr> to <abbr>We</abbr>',
            $weekDays->join(fn (WeekDay $day) => "<abbr>{$day->shortName()}</abbr>", rangeSeparator: ' to '),
        );
    }

    public function testJoinRendersOtherDaysAsAList(): void
    {
        $weekDays = new WeekDays([WeekDay::Monday, WeekDay::Wednesday, WeekDay::Friday]);

        $this->assertSame('Mo, We, Fr', $weekDays->join(fn (WeekDay $day) => $day->shortName()));
        $this->assertSame('Mo / We / Fr', $weekDays->join(fn (WeekDay $day) => $day->shortName(), listSeparator: ' / '));
    }

    public function testContainsComparesDays(): void
    {
        $weekDays = new WeekDays([WeekDay::Monday, WeekDay::Friday]);

        $this->assertTrue($weekDays->contains(WeekDay::Friday));
        $this->assertFalse($weekDays->contains(WeekDay::Tuesday));
    }
}
