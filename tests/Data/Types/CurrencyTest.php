<?php

namespace Hks\Schema\Data\Types;

use Hks\Schema\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

class CurrencyTest extends TestCase
{
    public static function minorUnitsProvider(): array
    {
        return [
            'USD' => [Currency::USD, 2],
            'EUR' => [Currency::EUR, 2],
            'JPY' => [Currency::JPY, 0],
            'GBP' => [Currency::GBP, 2],
            'CNY' => [Currency::CNY, 2],
            'CHF' => [Currency::CHF, 2],
        ];
    }

    #[DataProvider('minorUnitsProvider')]
    public function testMinorUnitsMatchesIsoFourTwoOneSevenForEveryOfferedCurrency(Currency $currency, int $expected): void
    {
        $this->assertSame($expected, $currency->minorUnits());
    }
}
