<?php

namespace Hks\Schema\Data\Types;

use Hks\Schema\Data\Contracts\Comparable;
use Hks\Schema\TestCase;

class LengthTest extends TestCase
{
    public function testImplementsComparable(): void
    {
        $this->assertInstanceOf(Comparable::class, new Length(5));
    }

    public function testAddCombinesTwoLengths(): void
    {
        $sum = (new Length(5))->add(new Length(2));

        $this->assertSame(7.0, $sum->value());
    }

    public function testSubtractSubtractsTwoLengths(): void
    {
        $difference = (new Length(5))->subtract(new Length(2));

        $this->assertSame(3.0, $difference->value());
    }

    public function testIsSmallerThanIsAnAliasForIsLessThan(): void
    {
        $this->assertTrue((new Length(2))->isSmallerThan(new Length(5)));
        $this->assertTrue((new Length(2))->isLessThan(new Length(5)));
    }

    public function testEqualsComparesTheConvertedValueNotTheRawOne(): void
    {
        $this->assertTrue((new Length(1, LengthUnit::Kilometer))->equals(new Length(1000, LengthUnit::Meter)));
    }

    public function testMinAndMaxCompareAcrossUnits(): void
    {
        $meter = new Length(500, LengthUnit::Meter);
        $kilometer = new Length(1, LengthUnit::Kilometer);

        $this->assertSame($meter, Length::min($meter, $kilometer));
        $this->assertSame($kilometer, Length::max($meter, $kilometer));
    }

    public function testLengthUnitOfResolvesASymbolAndPassesThroughAnInstance(): void
    {
        $this->assertSame(LengthUnit::Kilometer, LengthUnit::of('km'));
        $this->assertSame(LengthUnit::Kilometer, LengthUnit::of(LengthUnit::Kilometer));
        $this->assertNull(LengthUnit::of('bogus'));
    }
}
