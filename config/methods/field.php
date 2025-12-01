<?php

use Hks\Schema\Data\CurrencyFormatter;
use Hks\Schema\Data\NumberFormatter;
use Hks\Schema\Data\OpeningHoursFactory;
use Hks\Schema\Toolkit\Str;
use Kirby\Cms\App;
use Kirby\Cms\Html;
use Kirby\Content\Field;
use Kirby\Template\Snippet;
use Spatie\OpeningHours\OpeningHours;

return [

    /**
     * Append the given value to the field.
     */
    'append' => function (Field $field, mixed ...$values): Field {
        return $field->value(fn (?string $value) => $value . implode('', $values));
    },

    /**
     * Prepend the given values to the field.
     */
    'prepend' => function (Field $field, mixed ...$values): Field {
        return $field->value(fn (?string $value) => implode('', $values) . $value);
    },

    /**
     * Replace consecutive instances of a given character with a single character.
     */
    'deduplicate' => function (Field $field, string|array $characters = ' '): Field {
        return $field->value(fn (?string $value) => Str::deduplicate($value ?? '', $characters));
    },

    /**
     * Return the currency representation of the field value as a string.
     */
    'toCurrency' => function (Field $field, ?string $in = null, ?string $locale = null): string {
        return format(CurrencyFormatter::class, $field->value(), [
            'locale' => $locale,
            'currency' => $in,
        ]);
    },

    /**
     * Formats any number in a field value into a locale specific string.
     */
    'toNumber' => function (Field $field, ?int $precision = null, ?int $maxPrecision = null, ?string $locale = null): string {
        return format(NumberFormatter::class, $field->value(), [
            'locale' => $locale,
            'precision' => $precision,
            'maxPrecision' => $maxPrecision,
        ]);
    },

    /**
	 * Converts numerics an fractals in a field value into a decimal number.
     */
    'toDecimal' => function (Field $field, float $default = 0): float {
        if ($field->isEmpty()) {
            return $default;
        }

        $value = $field->value();

        if (is_numeric($value)) {
            return (float) $value;
        }

        if (! str_contains($value, '/')) {
            return $default;
        }

        [$numinator, $denominator] = array_map(
            floatval(...),
            explode('/', $value)
        );

        if ($numinator === 0 || $denominator === 0) {
            return $default;
        }

        return $numinator / $denominator;
    },

    /**
     * Converts the name of an SVG icon to the corresponding markup.
     */
    'toIcon' => function (Field $field, array $data = []): Snippet|string|null {
        $name = $field->value();

        if (! $name) {
            return null;
        }

        return App::instance()->snippet(['components/icon', 'icon'], [...$data, 'name' => $name]);
    },

    /**
     * Converts the field value to a stuctured data object representing the
     * opening hours of a local business.
     */
    'toOpeningHours' => function (Field $field): OpeningHours {
        return OpeningHoursFactory::createFromContent($field->toObject());
    },

    /**
     * Converts a custom link structure to HTML markup.
     */
    'toStructuredLink' => function (Field $field, string|array|null $text = null, array $attr = []): string {
        if (is_array($text)) {
            $attr = $text;
            $text = null;
        }

        if ($field->isEmpty()) {
            return $text ?? '';
        }

        $link = $field->toObject();

        $text ??= $attr['title'] ?? $link->title()->kirbytags();
        $href ??= $attr['href'] ?? $link->url()->toUrl();

        if (! $href) {
            return $text;
        }

        $icon = $this->icon()->toIcon();

        $attr += [
            'rel' => $link->rel()->value() ?: null,
            'target' => $link->target()->toBool() ? '_blank' : null,
        ];

        $page = App::instance()->site()->page();

        if ($href === $page?->url()) {
            $attr += [
                'aria-current' => 'page',
            ];
        }

        return Html::a($href, array_filter([$text, $icon]), $attr);
    },

];
