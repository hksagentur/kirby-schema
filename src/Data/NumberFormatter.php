<?php

namespace Hks\Schema\Data;

use NumberFormatter as IntlNumberFormatter;
use RuntimeException;

class NumberFormatter extends Formatter
{
    public function format(int|float $value, array $options = []): string
    {
        $this->ensureIntlExtensionIsInstalled();

        $formatter = new IntlNumberFormatter(
            locale: $options['locale'] ?? static::defaultLocale(),
            style: IntlNumberFormatter::DECIMAL,
        );

        if (isset($options['maxPrecision'])) {
            $formatter->setAttribute(IntlNumberFormatter::MAX_FRACTION_DIGITS, $options['maxPrecision']);
        } elseif (isset($options['precision'])) {
            $formatter->setAttribute(IntlNumberFormatter::FRACTION_DIGITS, $options['precision']);
        }

        return $formatter->format($value);
    }

    protected static function ensureIntlExtensionIsInstalled()
    {
        if (! extension_loaded('intl')) {
            throw new RuntimeException('The "intl" PHP extension is required to format currencies.');
        }
    }
}
