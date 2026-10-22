<?php

namespace Hks\Schema\Data\Types;

use Hks\Schema\TestCase;
use Kirby\Content\Field;

class AreaTest extends TestCase
{
    public function testAFieldConvertsToSquareMeters(): void
    {
        $area = (new Field(null, 'floorSize', '120'))->toDataType('area');

        $this->assertInstanceOf(Area::class, $area);
        $this->assertSame(120.0, $area->value());
        $this->assertSame(AreaUnit::SquareMeter, $area->unit());
    }

    public function testToLocaleStringKeepsTheUnitWithTheNumber(): void
    {
        $this->assertSame("1.234,5\u{00A0}m²", (new Area(1234.5))->toLocaleString('de'));
        $this->assertSame("120\u{00A0}m²", (new Area(119.6))->toLocaleString('de', ['maxPrecision' => 0]));
        $this->assertSame('120 m²', (new Area(120))->toLocaleString('de', ['separator' => ' ']));
    }

    public function testConvertsBetweenUnits(): void
    {
        $this->assertEqualsWithDelta(1.5, (new Area(15000))->toHectares()->value(), 0.0001);
        $this->assertEqualsWithDelta(1076.39, (new Area(100))->toSquareFeet()->value(), 0.01);
        $this->assertSame(AreaUnit::Hectare, (new Area(15000))->normalize()->unit());
    }

    public function testComparesAcrossUnits(): void
    {
        $this->assertTrue((new Area(1, AreaUnit::Hectare))->equals(new Area(10000)));
        $this->assertTrue((new Area(1, AreaUnit::Acre))->isSmallerThan(new Area(1, AreaUnit::Hectare)));
    }

    public function testRendersItsSnippet(): void
    {
        $html = (new Area(120))->toHtml();

        $this->assertStringContainsString('value="120"', $html);
        $this->assertStringContainsString('data-unit="m&sup2;"', $html);
    }
}
