<?php

use Hks\Schema\Data\CurrencyFormatter;
use Hks\Schema\Data\DecimalNumberFormatter;
use Holab\Data\OpeningHoursFactory;
use Kirby\Content\Field;
use Spatie\OpeningHours\OpeningHours;

return [
    'toCurrency' => function (Field $field, ?string $in = null, ?string $locale = null): string {
        return format(CurrencyFormatter::class, $field->value(), [
            'locale' => $locale,
            'currency' => $in,
        ]);
    },
    'toNumber' => function (Field $field, ?int $precision = null, ?int $maxPrecision = null, ?string $locale = null): string {
        return format(DecimalNumberFormatter::class, $field->value(), [
            'locale' => $locale,
            'precision' => $precision,
            'maxPrecision' => $maxPrecision,
        ]);
    },
    'toOpeningHours' => function (Field $field): OpeningHours {
        return OpeningHoursFactory::createFromContent($field->toObject());
    },
];
