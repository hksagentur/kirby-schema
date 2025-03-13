<?php

namespace Hks\Schema\Data;

use Kirby\Cms\App;
use Kirby\Toolkit\A;
use NumberFormatter;
use RuntimeException;

class DecimalNumberFormatter
{
    public function format(int|float $number, array $options = []): string
    {
        $this->ensureIntlExtensionIsInstalled();

        $options = A::merge([
            'locale' => App::instance()->language()->locale(LC_ALL),
            'precision' => null,
            'maxPrecision' => null,
        ], $options);

        $formatter = new NumberFormatter($options['locale'], NumberFormatter::DECIMAL);

        if (! is_null($options['maxPrecision'])) {
            $formatter->setAttribute(NumberFormatter::MAX_FRACTION_DIGITS, $options['maxPrecision']);
        } elseif (! is_null($options['precision'])) {
            $formatter->setAttribute(NumberFormatter::FRACTION_DIGITS, $options['precision']);
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
