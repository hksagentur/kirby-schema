<?php

use Hks\Schema\Data\AddressFormatter;
use Hks\Schema\Data\CurrencyFormatter;
use Hks\Schema\Data\GeoCoordinateFormatter;
use Hks\Schema\Data\NumberFormatter;
use Hks\Schema\Data\OpeningHoursFormatter;
use Kirby\Toolkit\A;

if (! function_exists('format')) {
    /**
     * Format a value with a given formatter.
     */
    function format(string $formatter, mixed $value, array $options = []): string
    {
        $formatters = A::merge([
            'address' => AddressFormatter::class,
            'coordinate' => GeoCoordinateFormatter::class,
            'currency' => CurrencyFormatter::class,
            'hours' => OpeningHoursFormatter::class,
            'number' => NumberFormatter::class,
        ], option('hksagentur.schema.formatters', []));

        $formatter = ! class_exists($formatter)
            ? ($formatters[$formatter] ?? null)
            : $formatter;

        if (! $formatter || ! class_exists($formatter)) {
            throw new InvalidArgumentException('Invalid formatter.');
        }

        $instance = new $formatter();

        if (! method_exists($instance, 'format')) {
            throw new InvalidArgumentException('Invalid formatter.');
        }

        return $instance->format($value, $options);
    }
}
