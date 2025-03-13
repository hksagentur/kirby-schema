<?php

namespace Hks\Schema\Data;

use NumberFormatter;
use RuntimeException;

class CurrencyFormatter extends Formatter
{
    protected static string $currency = 'EUR';

    public static function defaultCurrency(): string
    {
        return static::$currency;
    }

    public static function useCurrency(string $currency): void
    {
        static::$currency = $currency;
    }

    public static function withCurrency(string $currency, callable $callback): void
    {
        $previousCurrency = static::defaultCurrency();

        static::useCurrency($currency);

        $callback();

        static::useCurrency($previousCurrency);
    }

    public function format(int|float $value, array $options = []): string
    {
        $this->ensureIntlExtensionIsInstalled();

        $formatter = new NumberFormatter(
            locale: $options['locale'] ?? static::defaultLocale(),
            style: NumberFormatter::CURRENCY,
        );

        return $formatter->formatCurrency(
            amount: $value,
            currency: $options['currency'] ?? static::defaultCurrency(),
        );
    }

    protected static function ensureIntlExtensionIsInstalled()
    {
        if (! extension_loaded('intl')) {
            throw new RuntimeException('The "intl" PHP extension is required to format currencies.');
        }
    }
}
