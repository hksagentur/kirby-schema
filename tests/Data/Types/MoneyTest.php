<?php

namespace Hks\Schema\Data\Types;

use Hks\Schema\Data\Contracts\Comparable;
use Hks\Schema\Data\Contracts\Schemable;
use Hks\Schema\Data\Exceptions\NotFoundException;
use Hks\Schema\TestCase;
use InvalidArgumentException;
use Kirby\Cms\App;

class MoneyTest extends TestCase
{
    public function testImplementsComparable(): void
    {
        $this->assertInstanceOf(Comparable::class, new Money(1999, Currency::from('EUR')));
    }

    public function testImplementsSchemable(): void
    {
        $this->assertInstanceOf(Schemable::class, new Money(1999, Currency::from('EUR')));
    }

    public function testValueIsAnAliasForAmountInMinorUnits(): void
    {
        $money = new Money(1999, Currency::from('EUR'));

        $this->assertSame(1999, $money->value());
        $this->assertSame($money->amount(), $money->value());
    }

    public function testToMajorUnitsConvertsToTheHumanFacingDecimalAmount(): void
    {
        $this->assertSame(19.99, (new Money(1999, Currency::from('EUR')))->toMajorUnits());
    }

    public function testToFloatIsAnAliasForToMajorUnits(): void
    {
        $money = new Money(1999, Currency::from('EUR'));

        $this->assertSame($money->toMajorUnits(), $money->toFloat());
    }

    public function testToIntIsAnAliasForAmount(): void
    {
        $money = new Money(1999, Currency::from('EUR'));

        $this->assertSame($money->amount(), $money->toInt());
    }

    public function testFromArrayResolvesTheAmountAndCurrency(): void
    {
        $money = Money::from(['amount' => 19.99, 'currency' => 'USD']);

        $this->assertSame(1999, $money->amount());
        $this->assertSame(19.99, $money->toMajorUnits());
        $this->assertSame('USD', $money->currency()->code());
    }

    public function testFromArrayResolvesCurrencyCaseInsensitively(): void
    {
        $money = Money::from(['amount' => 19.99, 'currency' => 'usd']);

        $this->assertSame('USD', $money->currency()->code());
    }

    public function testFromMinorUnitsConstructsFromAnAlreadyIntegerAmount(): void
    {
        $money = Money::fromMinorUnits(1999, Currency::from('USD'));

        $this->assertSame(1999, $money->amount());
        $this->assertSame(19.99, $money->toMajorUnits());
    }

    public function testFromArrayDefaultsToEuroWhenNoCurrencyIsGiven(): void
    {
        $money = Money::from(['amount' => 5]);

        $this->assertSame('EUR', $money->currency()->code());
    }

    public function testConstructorWithoutACurrencyUsesTheSameConfigurableDefaultAsFromArray(): void
    {
        // Regression: the constructor default used to be the hardcoded literal `Currency::from('EUR')`,
        // independent of `resolveCurrency()`'s (now configurable) default — `new Money($amount)`
        // and `Money::from(['amount' => $amount])` could silently disagree on the assumed currency.
        $this->assertTrue(Currency::default()->equals((new Money(500))->currency()));
    }

    public function testCurrencyDefaultFallsBackToEuroWithoutAnOptionOverride(): void
    {
        $this->assertSame('EUR', Currency::default()->code());
    }

    public function testFromMajorUnitsRoundsToTheCurrencysMinorUnitPrecision(): void
    {
        // JPY has no minor unit — the fractional yen must round away, not truncate silently.
        $this->assertSame(100, Money::fromMajorUnits(99.6, Currency::from('JPY'))->amount());
    }

    public function testEqualsComparesAmountAndCurrency(): void
    {
        $money = new Money(1999, Currency::from('EUR'));
        $other = new Money(1999, Currency::from('EUR'));

        $this->assertTrue($money->equals($other));
    }

    public function testEqualsIsFalseWhenCurrenciesDiffer(): void
    {
        $money = new Money(1999, Currency::from('EUR'));
        $other = new Money(1999, Currency::from('USD'));

        $this->assertFalse($money->equals($other));
    }

    public function testCompareOrdersAmountsWithinTheSameCurrency(): void
    {
        $this->assertTrue((new Money(500, Currency::from('EUR')))->isLessThan(new Money(1000, Currency::from('EUR'))));
        $this->assertTrue((new Money(1000, Currency::from('EUR')))->isGreaterThan(new Money(500, Currency::from('EUR'))));
    }

    public function testCompareThrowsWhenComparingDifferentCurrencies(): void
    {
        $this->expectException(InvalidArgumentException::class);

        (new Money(500, Currency::from('EUR')))->compare(new Money(500, Currency::from('USD')));
    }

    public function testAssertSameCurrencyIsUsablePubliclyAsAStandaloneGuard(): void
    {
        Money::assertSameCurrency(new Money(500, Currency::from('EUR')), new Money(1000, Currency::from('EUR')));

        $this->expectException(InvalidArgumentException::class);

        Money::assertSameCurrency(new Money(500, Currency::from('EUR')), new Money(500, Currency::from('USD')));
    }

    public function testAmountOfIsUsablePubliclyToResolveAnOperandsMinorUnits(): void
    {
        $money = new Money(1000, Currency::from('EUR'));

        $this->assertSame(250, $money->amountOf(2.5));
        $this->assertSame(500, $money->amountOf(new Money(500, Currency::from('EUR'))));
    }

    public function testCompareInterpretsABareNumberAsMajorUnitsInTheReceiversCurrency(): void
    {
        // Regression: `compare()` used to resolve a bare number via `tryFrom()`, which defaults
        // to EUR — so comparing a USD amount against a bare number threw a bogus currency
        // mismatch instead of comparing within the receiver's own currency.
        $this->assertTrue((new Money(1000, Currency::from('USD')))->isGreaterThan(5));
    }

    public function testMinAndMaxPickTheSmallestAndLargestAmount(): void
    {
        $amounts = [new Money(1000, Currency::from('EUR')), new Money(250, Currency::from('EUR')), new Money(500, Currency::from('EUR'))];

        $this->assertSame(250, Money::min(...$amounts)->amount());
        $this->assertSame(1000, Money::max(...$amounts)->amount());
    }

    public function testAddCombinesTwoAmountsInTheSameCurrency(): void
    {
        $sum = (new Money(1000, Currency::from('EUR')))->add(new Money(250, Currency::from('EUR')));

        $this->assertSame(1250, $sum->amount());
    }

    public function testAddInterpretsABareNumberAsMajorUnitsInTheReceiversCurrency(): void
    {
        $sum = (new Money(1000, Currency::from('EUR')))->add(2.5);

        $this->assertSame(1250, $sum->amount());
    }

    public function testAddThrowsWhenCombiningDifferentCurrencies(): void
    {
        $this->expectException(InvalidArgumentException::class);

        (new Money(1000, Currency::from('EUR')))->add(new Money(250, Currency::from('USD')));
    }

    public function testSubtractCombinesTwoAmountsInTheSameCurrency(): void
    {
        $difference = (new Money(1000, Currency::from('EUR')))->subtract(new Money(250, Currency::from('EUR')));

        $this->assertSame(750, $difference->amount());
    }

    public function testMultiplyByScalesTheAmountAndRoundsToWholeMinorUnits(): void
    {
        $tripled = (new Money(1000, Currency::from('EUR')))->multiplyBy(3);

        $this->assertSame(3000, $tripled->amount());
    }

    public function testDivideByScalesTheAmountAndRoundsToWholeMinorUnits(): void
    {
        $third = (new Money(1000, Currency::from('EUR')))->divideBy(3);

        $this->assertSame(333, $third->amount());
    }

    public function testToStringRendersTheCanonicalAmountAndIsoCode(): void
    {
        $this->assertSame('19.99 EUR', (new Money(1999, Currency::from('EUR')))->toString());
    }

    public function testToStringOmitsTheDecimalPointForACurrencyWithNoMinorUnit(): void
    {
        // JPY's minorUnits() is 0 — the format string is built dynamically from it, so this
        // guards against a hardcoded "%.2f" ever creeping back in.
        $this->assertSame('1500 JPY', Money::fromMajorUnits(1500, Currency::from('JPY'))->toString());
    }

    public function testToLocaleStringRendersALocalizedCurrencyAmount(): void
    {
        $formatted = (new Money(1999, Currency::from('USD')))->toLocaleString('en_US');

        $this->assertStringContainsString('19.99', $formatted);
        $this->assertStringContainsString('$', $formatted);
    }

    public function testToStructuredDataRendersAMonetaryAmountNode(): void
    {
        $this->assertSame([
            '@type' => 'MonetaryAmount',
            'currency' => 'EUR',
            'value' => 19.99,
        ], (new Money(1999, Currency::from('EUR')))->toStructuredData());
    }

    public function testDefaultCurrencyThrowsForAnUnsupportedOption(): void
    {
        $this->app = new App([
            'roots' => ['index' => '/dev/null'],
            'options' => ['hksagentur.schema.defaultCurrency' => 'EURO'],
        ]);

        $this->expectException(NotFoundException::class);

        Currency::default();
    }

    public function testIsSameCurrencyComparesTheCurrencyRegardlessOfTheAmount(): void
    {
        $money = new Money(500, Currency::from('EUR'));

        $this->assertTrue($money->isSameCurrency(new Money(1000, Currency::from('EUR'))));
        $this->assertFalse($money->isSameCurrency(new Money(500, Currency::from('USD'))));
    }
}
