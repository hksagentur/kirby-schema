<?php

namespace Hks\Schema\Data\Types;

use Hks\Schema\TestCase;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;

class CurrencyTest extends TestCase
{
    public static function minorUnitsProvider(): array
    {
        return [
            'EUR' => ['EUR', 2],
            'JPY' => ['JPY', 0],
            'KWD' => ['KWD', 3],
            'SEK' => ['SEK', 2],
        ];
    }

    #[DataProvider('minorUnitsProvider')]
    public function testMinorUnitsMatchIsoFourTwoOneSeven(string $code, int $expected): void
    {
        $this->assertSame($expected, Currency::from($code)->minorUnits());
    }

    public function testAcceptsAnyIsoCurrencyCodeCaseInsensitively(): void
    {
        $this->assertSame('SEK', Currency::from('sek')->code());
    }

    public function testRejectsAValueThatIsNoCurrencyCode(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Currency::from('EURO');
    }

    public function testTryFromReturnsNullForAValueThatIsNoCurrencyCode(): void
    {
        $this->assertNull(Currency::tryFrom('EURO'));
        $this->assertNull(Currency::tryFrom(''));
    }

    public function testSymbolDependsOnTheLocale(): void
    {
        $this->assertSame('€', Currency::from('EUR')->symbol('de'));
        $this->assertSame('CHF', Currency::from('CHF')->symbol('de_CH'));
        $this->assertSame('$', Currency::from('USD')->symbol('en_US'));
    }

    public function testToLocaleStringReturnsTheTranslatedName(): void
    {
        $this->assertSame('Schweizer Franken', Currency::from('CHF')->toLocaleString('de'));
        $this->assertSame('Swiss Franc', Currency::from('CHF')->toLocaleString('en'));
    }

    public function testToLocaleStringFallsBackToTheCodeWithoutATranslation(): void
    {
        $this->assertSame('SEK', Currency::from('SEK')->toLocaleString('de'));
    }

    public function testEqualsComparesTheCode(): void
    {
        $this->assertTrue(Currency::from('eur')->equals(Currency::from('EUR')));
        $this->assertFalse(Currency::from('EUR')->equals(Currency::from('USD')));
    }

    public function testToHtmlAbbreviatesTheCodeWithItsName(): void
    {
        $this->assertSame('<abbr title="Euro">EUR</abbr>', trim(Currency::from('EUR')->toHtml()));
    }
}
