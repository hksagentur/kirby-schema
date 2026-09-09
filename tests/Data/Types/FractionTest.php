<?php

namespace Hks\Schema\Data\Types;

use Hks\Schema\Concerns\AssertsHtml;
use Hks\Schema\Data\Contracts\Comparable;
use Hks\Schema\Data\Contracts\Htmlable;
use Hks\Schema\TestCase;

class FractionTest extends TestCase
{
    use AssertsHtml;

    public function testImplementsComparable(): void
    {
        $this->assertInstanceOf(Comparable::class, Fraction::from('1/2'));
    }

    public function testImplementsHtmlable(): void
    {
        $this->assertInstanceOf(Htmlable::class, Fraction::from('1/2'));
    }

    public function testValueIsAnAliasForToFloat(): void
    {
        $this->assertSame(Fraction::from('1/2')->toFloat(), Fraction::from('1/2')->value());
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

    public function testToStringReturnsAPlainFormWithoutHtmlEntities(): void
    {
        $fraction = new Fraction(16, 9);

        $this->assertSame('16/9', $fraction->toString());
        $this->assertStringNotContainsString('&frasl;', $fraction->toString());
    }

    public function testToHtmlStillRendersTheFractionSlashCharacter(): void
    {
        $fraction = new Fraction(16, 9);

        $this->assertSameHtml('<span role="math">16 ⁄9</span>', $fraction->toHtml());
    }

    public function testEqualsUsesThePlainStringForm(): void
    {
        $fraction = new Fraction(16, 9);

        $this->assertTrue($fraction->isEqualTo(Fraction::from('16/9')));
    }

    public function testJsonSerializeMatchesToArray(): void
    {
        $fraction = new Fraction(16, 9);

        $this->assertSame($fraction->toArray(), $fraction->jsonSerialize());
        $this->assertSame(
            $fraction->toArray(),
            json_decode(json_encode($fraction), true),
        );
    }

    public function testHasNumeratorAndHasDenominatorCheckTheStoredValuesAsGiven(): void
    {
        $fraction = new Fraction(4, 2);

        $this->assertTrue($fraction->hasNumerator(4));
        $this->assertTrue($fraction->hasDenominator(2));
        $this->assertFalse($fraction->hasDenominator(1));
    }

    public function testToStringRendersAnUnsimplifiedFractionAsGiven(): void
    {
        $fraction = new Fraction(4, 2);

        $this->assertSame('4/2', $fraction->toString());
    }
}
