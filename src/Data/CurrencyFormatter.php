<?php

namespace Hks\Schema\Data;

use Kirby\Cms\App;
use Kirby\Toolkit\A;
use NumberFormatter;
use RuntimeException;

class CurrencyFormatter
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

    public function format(int|float $number, array $options = []): string
    {
        $this->ensureIntlExtensionIsInstalled();

        $options = A::merge([
            'locale' => App::instance()->language()->locale(LC_ALL),
            'currency' => static::defaultCurrency(),
        ], $options);

        $formatter = new NumberFormatter($options['locale'], NumberFormatter::CURRENCY);

        return $formatter->formatCurrency($number, $options['currency']);
    }

    protected static function ensureIntlExtensionIsInstalled()
    {
        if (! extension_loaded('intl')) {
            throw new RuntimeException('The "intl" PHP extension is required to format currencies.');
        }
    }
}
