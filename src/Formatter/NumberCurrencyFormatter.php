<?php

namespace Hks\Schema\Formatter;

use RuntimeException;
use NumberFormatter as IntlNumberFormatter;

/**
 * @extends Formatter<int|float>
 */
class NumberCurrencyFormatter extends Formatter
{
    public function defaults(): array
    {
        return [
            'locale' => $this->kirby()->language()->locale(LC_MONETARY),
            'currency' => 'EUR',
        ];
    }

    public function format(mixed $amount): string
    {
        $this->ensureIntlExtensionIsInstalled();

        $locale = $this->option('locale');

        $formatter = new IntlNumberFormatter($locale,IntlNumberFormatter::CURRENCY);

        $currency = $this->option('currency');

        return $formatter->formatCurrency($amount, $currency);
    }

    protected static function ensureIntlExtensionIsInstalled()
    {
        if (! extension_loaded('intl')) {
            throw new RuntimeException('The "intl" PHP extension is required to format currencies.');
        }
    }
}
