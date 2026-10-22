<?php

namespace Hks\Schema\Toolkit;

use InvalidArgumentException;
use Kirby\Toolkit\I18n;
use NumberFormatter;

class Number
{
    /**
     * Formats a number for a locale.
     *
     * @param array{precision?: int, maxPrecision?: int, roundingMode?: string, roundingIncrement?: int|float} $options
     */
    public static function format(int|float $number, ?string $locale = null, array $options = []): string
    {
        $formatter = new NumberFormatter($locale ?? I18n::locale(), NumberFormatter::DECIMAL);

        if (isset($options['maxPrecision'])) {
            $formatter->setAttribute(NumberFormatter::MAX_FRACTION_DIGITS, $options['maxPrecision']);
        } elseif (isset($options['precision'])) {
            $formatter->setAttribute(NumberFormatter::FRACTION_DIGITS, $options['precision']);
        }

        if (isset($options['roundingIncrement'])) {
            $formatter->setAttribute(NumberFormatter::ROUNDING_INCREMENT, $options['roundingIncrement']);
        }

        if (isset($options['roundingMode'])) {
            $formatter->setAttribute(NumberFormatter::ROUNDING_MODE, static::roundingMode($options['roundingMode']));
        }

        return $formatter->format($number);
    }

    /**
     * Formats a range of numbers, e.g. `from 120`, `up to 180` or `120–180`.
     *
     * Bounds are rounded outwards, so a rounded bound never excludes the actual value.
     *
     * @param array{precision?: int, maxPrecision?: int, roundingMode?: string, roundingIncrement?: int|float} $options
     */
    public static function range(int|float|null $from, int|float|null $to, ?string $locale = null, array $options = []): string
    {
        $format = fn (int|float $number, string $roundingMode): string => static::format($number, $locale, [
            'roundingMode' => $roundingMode,
            ...$options,
        ]);

        return match (true) {
            $from === null && $to === null => '',
            $to === null => I18n::template('hksagentur.schema.range.from', null, [
                'from' => $format($from, 'floor'),
            ], $locale),
            $from === null => I18n::template('hksagentur.schema.range.to', null, [
                'to' => $format($to, 'ceiling'),
            ], $locale),
            $from == $to => $format($from, 'halfUp'),
            default => I18n::template('hksagentur.schema.range.between', null, [
                'from' => $format($from, 'floor'),
                'to' => $format($to, 'ceiling'),
            ], $locale),
        };
    }

    /**
     * Maps a rounding mode name to its `NumberFormatter` constant.
     */
    protected static function roundingMode(string $mode): int
    {
        return match ($mode) {
            'ceiling' => NumberFormatter::ROUND_CEILING,
            'floor' => NumberFormatter::ROUND_FLOOR,
            'up' => NumberFormatter::ROUND_UP,
            'down' => NumberFormatter::ROUND_DOWN,
            'halfUp' => NumberFormatter::ROUND_HALFUP,
            'halfDown' => NumberFormatter::ROUND_HALFDOWN,
            'halfEven' => NumberFormatter::ROUND_HALFEVEN,
            default => throw new InvalidArgumentException(sprintf('Invalid rounding mode "%s".', $mode)),
        };
    }
}
