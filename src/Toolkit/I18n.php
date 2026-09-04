<?php

namespace Hks\Schema\Toolkit;

use Kirby\Toolkit\A;
use Kirby\Toolkit\Str;

class I18n extends \Kirby\Toolkit\I18n
{
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
}
