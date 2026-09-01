<?php

namespace Hks\Schema\Data\Types;

use Hks\Schema\TestCase;

class WeekDayTest extends TestCase
{
    public function testFromNameMatchesCaseInsensitively(): void
    {
        $this->assertSame(WeekDay::Monday, WeekDay::fromName('Monday'));
        $this->assertSame(WeekDay::Monday, WeekDay::fromName('MONDAY'));
    }

    public function testFromNameReturnsNullGivenAnUnknownName(): void
    {
        $this->assertNull(WeekDay::fromName('not a day'));
    }

    public function testToStringReturnsTheEnglishCaseName(): void
    {
        $this->assertSame('Monday', WeekDay::Monday->toString());
    }

    public function testToLocaleStringTranslatesTheLabelPerLocale(): void
    {
        $this->assertSame('Monday', WeekDay::Monday->toLocaleString('en', ['style' => 'full']));
        $this->assertSame('Montag', WeekDay::Monday->toLocaleString('de', ['style' => 'full']));
    }

    public function testCompareOrdersByIsoDayOfWeek(): void
    {
        $this->assertTrue(WeekDay::Monday->compare(WeekDay::Tuesday) < 0);
        $this->assertTrue(WeekDay::Sunday->compare(WeekDay::Monday) > 0);
    }

    public function testFollowsAndPrecedesDescribeAdjacentDays(): void
    {
        $this->assertTrue(WeekDay::Tuesday->follows(WeekDay::Monday));
        $this->assertTrue(WeekDay::Monday->precedes(WeekDay::Tuesday));
        $this->assertTrue(WeekDay::Monday->follows(WeekDay::Sunday));
        $this->assertFalse(WeekDay::Wednesday->follows(WeekDay::Monday));
    }
}
