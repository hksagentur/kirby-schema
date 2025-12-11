<?php

namespace Hks\Schema\Formatter;

use RuntimeException;
use NumberFormatter as IntlNumberFormatter;

/**
 * @extends Formatter<int|float>
 */
class NumberDecimalFormatter extends Formatter
{
    public function defaults(): array
    {
        return [
            'locale' => $this->kirby()->language()->locale(LC_NUMERIC),
            'precision' => null,
            'maxPrecision' => null,
        ];
    }

    public function format(mixed $number): string
    {
        $this->ensureIntlExtensionIsInstalled();

        $locale = $this->option('locale');

        $formatter = new IntlNumberFormatter($locale,IntlNumberFormatter::DECIMAL);

        $maxPrecision = $this->option('maxPrecision');
        $precision = $this->option('precision');

        if (! is_null($maxPrecision)) {
            $formatter->setAttribute(IntlNumberFormatter::MAX_FRACTION_DIGITS, $maxPrecision);
        } elseif (! is_null($precision)) {
            $formatter->setAttribute(IntlNumberFormatter::FRACTION_DIGITS, $precision);
        }

        return $formatter->format($number);
    }

    protected static function ensureIntlExtensionIsInstalled()
    {
        if (! extension_loaded('intl')) {
            throw new RuntimeException('The "intl" PHP extension is required to format currencies.');
        }
    }
}
