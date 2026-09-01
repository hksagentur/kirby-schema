<?php

namespace Hks\Schema\Data\Types;

use Hks\Schema\Data\Exceptions\ValidationException;
use Hks\Schema\TestCase;

class OpeningHoursSpecificationTest extends TestCase
{
    public function testFromCreatesANewInstanceFromRawArrayDefinitions(): void
    {
        $specification = OpeningHoursSpecification::from([
            'weekDays' => ['Monday', 'Tuesday'],
            'timeRanges' => [['09:00', '17:00']],
        ]);

        $this->assertSame(
            [WeekDay::Monday, WeekDay::Tuesday],
            $specification->weekDays()->toArray(),
        );
        $this->assertSame('09:00:00/17:00:00', $specification->timeRanges()->toString());
    }

    public function testFromThrowsGivenNoWeekDays(): void
    {
        $this->expectException(ValidationException::class);

        OpeningHoursSpecification::from([
            'weekDays' => [],
            'timeRanges' => [['09:00', '17:00']],
        ]);
    }

    public function testTryFromReturnsNullGivenNoWeekDays(): void
    {
        $this->assertNull(OpeningHoursSpecification::tryFrom([
            'weekDays' => [],
            'timeRanges' => [['09:00', '17:00']],
        ]));
    }

    public function testToArrayRoundTripsThroughFrom(): void
    {
        $specification = OpeningHoursSpecification::from([
            'weekDays' => ['Monday', 'Tuesday'],
            'timeRanges' => [['09:00', '17:00']],
            'data' => ['note' => 'Kitchen closes early'],
        ]);

        $roundTripped = OpeningHoursSpecification::from($specification->toArray());

        $this->assertSame($specification->weekDays()->toArray(), $roundTripped->weekDays()->toArray());
        $this->assertSame($specification->timeRanges()->toString(), $roundTripped->timeRanges()->toString());
        $this->assertSame($specification->data(), $roundTripped->data());
    }

    public function testToStringRendersWeekDaysAndTimeRangesAsIsoValues(): void
    {
        $specification = OpeningHoursSpecification::from([
            'weekDays' => ['Monday', 'Tuesday'],
            'timeRanges' => [['09:00', '17:00']],
        ]);

        $this->assertSame('Mo-Tu: 09:00:00/17:00:00', $specification->toString());
    }

    public function testToHtmlRendersATimeTagWithAnIsoDatetimeAttribute(): void
    {
        $specification = OpeningHoursSpecification::from([
            'weekDays' => ['Monday'],
            'timeRanges' => [['09:00', '17:00']],
        ]);

        $html = $specification->toHtml();

        // The tag body goes through locale-aware time formatting (ICU), so only
        // the deterministic parts are asserted here: the tag itself, the ISO
        // datetime attribute, and the weekday label.
        $this->assertStringStartsWith('<time datetime="Mo: 09:00:00/17:00:00">', $html);
        $this->assertStringEndsWith('</time>', $html);
        $this->assertStringContainsString('Mo:', $html);
    }
}
