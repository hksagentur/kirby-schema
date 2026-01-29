<?php

namespace Hks\Schema\Toolkit;

use IntlDateFormatter;
use Kirby\Toolkit\A;
use Kirby\Toolkit\Date;

class I18n extends \Kirby\Toolkit\I18n
{
    /** @var array<string, IntlDateFormatter> */
    protected static array $dateFormatters = [];

    public static function getDayNames(?string $locale = null, string $format = 'full'): array
    {
        $pattern = match ($format) {
            'short' => 'EEE',
            'tiny' => 'EEEEE',
            'initial' => 'EEEEEE',
            default => 'EEEE',
        };

        $formatter = static::dateFormatter(
            locale: $locale ?? static::locale(),
            pattern: $pattern,
        );

        $dayOfWeek = new Date('next Monday');
        $dayNames = [];

        for ($i = 1; $i <= 7; $i++) {
            $dayNames[] = $formatter->format($dayOfWeek);
            $dayOfWeek->modify('+1 day');
        }

        return $dayNames;
    }

    public static function oxfordList(array $values, ?int $limit = null, ?string $locale = null): string
    {
        $total = count($values);

        if ($total === 0) {
            return '';
        }

        if ($total === 1) {
            return (string) A::first($values);
        }

        if ($total === 2) {
            return I18n::template('hksagentur.schema.oxfordList.two', replace: [
                'first' => (string) A::first($values),
                'second' => (string) A::last($values),
            ], locale: $locale);
        }

        if ($limit === null || $limit >= $total) {
            return I18n::template('hksagentur.schema.oxfordList.many', replace: [
                'list' => (string) A::join(A::slice($values, 0, -1)),
                'last' => (string) A::last($values),
            ], locale: $locale);
        }

        $items = A::slice(A::map($values, strval(...)), 0, $limit);

        $message = I18n::translateCount(
            key: 'hksagentur.schema.oxfordList.truncated',
            count: $total - count($items),
            locale: $locale,
        );

        $message = Str::template($message, ['list' => A::join($items)]);

        return $message;
    }

    protected static function dateFormatter(string $locale, string $pattern): ?IntlDateFormatter
    {
        if (! extension_loaded('intl') || ! class_exists(IntlDateFormatter::class)) {
            return null;
        }

        $formatter = static::$dateFormatters[$locale][$pattern] ?? null;

        if ($formatter) {
            return $formatter;
        }

        return static::$dateFormatters[$locale][$pattern] = new IntlDateFormatter($locale, pattern: $pattern);
    }

}
