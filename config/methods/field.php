<?php

use Hks\Schema\Data\Types\DataType;
use Hks\Schema\Toolkit\I18n;
use Hks\Schema\Toolkit\Str;
use Kirby\Content\Field;

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
     * Converts a field value to a data type.
     */
    'toDataType' => function (Field $field, string $name): ?DataType {

    },

    /**
     * Converts a field value to a data object.
     */
    'toDataObject' => function (Field $field, string $name): ?DataObject {

    },

];
