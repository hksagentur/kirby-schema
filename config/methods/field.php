<?php

use Hks\Schema\Data\Factory\GeoCoordinateFactory;
use Hks\Schema\Data\Factory\OpeningHoursFactory;
use Hks\Schema\Data\Factory\OrganizationFactory;
use Hks\Schema\Data\Factory\PersonFactory;
use Hks\Schema\Data\Factory\PostalAddressFactory;
use Hks\Schema\Data\Fraction;
use Hks\Schema\Data\GeoCoordinate;
use Hks\Schema\Data\Organization;
use Hks\Schema\Data\Person;
use Hks\Schema\Data\PostalAddress;
use Hks\Schema\Toolkit\I18n;
use Hks\Schema\Toolkit\Str;
use Kirby\Content\Field;
use Spatie\OpeningHours\OpeningHours;

return [

    // Manipulators

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
     * Formats an array as a human-readable list using commas and a final "and".
     */
    'toOxfordList' => function (Field $field, ?int $limit = null, ?string $locale = null): Field {
        return $field->value(function (?string $value) use ($limit, $locale): string {
            return I18n::oxfordList(Str::split($value ?? ''), $limit, $locale);
        });
    },

    // Converters

    /**
     * Converts a numeric field value to a fraction object.
     */
    'toFraction' => function (Field $field, int|string $default = 0): Fraction {
        return Fraction::of($field->or($default)->value());
    },

    /**
     * Parses a field value as yaml data and converts it to a geo coordinate object.
     */
    'toGeoCoordinate' => function (Field $field): GeoCoordinate {
        return GeoCoordinateFactory::createFromContent($field->toObject());
    },

    /**
     * Parses a field value as yaml data and converts it to an opening hours object.
     */
    'toOpeningHours' => function (Field $field): OpeningHours {
        return OpeningHoursFactory::createFromContent($field->toObject());
    },

    /**
     * Parses a field value as yaml data and converts it to an organization object.
     */
    'toOrganization' => function (Field $field): Organization {
        return OrganizationFactory::createFromContent($field->toObject());
    },

    /**
     * Parses a field value as yaml data and converts it to a person object.
     */
    'toPerson' => function (Field $field): Person {
        return PersonFactory::createFromContent($field->toObject());
    },

    /**
     * Parses a field value as yaml data and converts it to a postal address object.
     */
    'toPostalAddress' => function (Field $field): PostalAddress {
        return PostalAddressFactory::createFromContent($field->toObject());
    },

    // 'toFormattedLink' => function (Field $field, string|array|null $text = null, array $attr = []): string {
    //     if (is_array($text)) {
    //         $attr = $text;
    //         $text = null;
    //     }

    //     if ($field->isEmpty()) {
    //         return $text ?? '';
    //     }

    //     $link = $field->toObject();

    //     $text ??= $attr['title'] ?? $link->title()->kirbytags();
    //     $href ??= $attr['href'] ?? $link->url()->toUrl();

    //     if (! $href) {
    //         return $text;
    //     }

    //     $icon = $link->icon()->toIcon();

    //     $attr += [
    //         'rel' => $link->rel()->value() ?: null,
    //         'target' => $link->target()->toBool() ? '_blank' : null,
    //     ];

    //     $page = App::instance()->site()->page();

    //     if ($href === $page?->url()) {
    //         $attr += [
    //             'aria-current' => 'page',
    //         ];
    //     }

    //     return Html::a($href, array_filter([$text, $icon]), $attr);
    // },

];
