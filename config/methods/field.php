<?php

use Hks\Schema\Data\CurrencyFormatter;
use Hks\Schema\Data\NumberFormatter;
use Hks\Schema\Data\OpeningHoursFactory;
use Hks\Schema\Toolkit\Str;
use Kirby\Content\Field;
use Spatie\OpeningHours\OpeningHours;

return [
    'append' => function (Field $field, mixed ...$values): Field {
        return $field->value(fn (?string $value) => $value . implode('', $values));
    },
    'prepend' => function (Field $field, mixed ...$values): Field {
        return $field->value(fn (?string $value) => implode('', $values) . $value);
    },
    'deduplicate' => function (Field $field, string|array $characters = ' '): Field {
        return $field->value(fn (?string $value) => Str::deduplicate($value ?? '', $characters));
    },
    'toCurrency' => function (Field $field, ?string $in = null, ?string $locale = null): string {
        return format(CurrencyFormatter::class, $field->value(), [
            'locale' => $locale,
            'currency' => $in,
        ]);
    },
    'toNumber' => function (Field $field, ?int $precision = null, ?int $maxPrecision = null, ?string $locale = null): string {
        return format(NumberFormatter::class, $field->value(), [
            'locale' => $locale,
            'precision' => $precision,
            'maxPrecision' => $maxPrecision,
        ]);
    },
    'toOpeningHours' => function (Field $field): OpeningHours {
        return OpeningHoursFactory::createFromContent($field->toObject());
    },
];
