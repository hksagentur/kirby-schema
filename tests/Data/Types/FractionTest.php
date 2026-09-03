<?php

namespace Hks\Schema\Data\Types;

use Hks\Schema\Data\Contracts\Comparable;
use Hks\Schema\TestCase;

class FractionTest extends TestCase
{
    public function testImplementsComparable(): void
    {
        $this->assertInstanceOf(Comparable::class, Fraction::from('1/2'));
    }

    public function testCompareAcceptsAFloatWithoutLosingPrecision(): void
    {
        $this->assertTrue(Fraction::from('1/2')->compare(0.6) < 0);
    }

    public function testCompareBasedSortOrdersFractionsAscending(): void
    {
        $fractions = [
            Fraction::from('3/4'),
            Fraction::from('1/2'),
            Fraction::from('5/6'),
        ];

        usort($fractions, fn (Fraction $a, Fraction $b) => $a->compare($b));

        $this->assertSame(
            ['1/2', '3/4', '5/6'],
            array_map(fn (Fraction $fraction) => "{$fraction->numerator()}/{$fraction->denominator()}", $fractions),
        );
    }
}
