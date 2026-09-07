<?php

use Hks\Schema\Data\DataObject;
use Hks\Schema\Data\Exceptions\NotFoundException;
use Hks\Schema\Data\Exceptions\ValidationException;
use Hks\Schema\Data\Registry;
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
        $class = Registry::dataType($name);

        if (! $class) {
            throw NotFoundException::for('No data type is registered for "%s".', $name);
        }

        if ($field->isEmpty()) {
            return null;
        }

        $value = $class::acceptsArrayInput() ? $field->yaml() : $field->toString();

        try {
            return $class::from($value);
        } catch (Throwable $exception) {
            $message = sprintf('Invalid %s data for "%s" field', $name, $field->key());

            if ($parent = $field->parent()) {
                $message .= sprintf(' on parent "%s"', $parent->id());
            }

            throw new ValidationException($message, previous: $exception);
        }
    },

    /**
     * Converts a field value to a data object.
     */
    'toDataObject' => function (Field $field, string $name): ?DataObject {
        $class = Registry::dataObject($name);

        if (! $class) {
            throw NotFoundException::for('No data object is registered for "%s".', $name);
        }

        if ($field->isEmpty()) {
            return null;
        }

        try {
            return $class::fromArray($field->yaml());
        } catch (Throwable $exception) {
            $message = sprintf('Invalid %s data for "%s" field', $name, $field->key());

            if ($parent = $field->parent()) {
                $message .= sprintf(' on parent "%s"', $parent->id());
            }

            throw new ValidationException($message, previous: $exception);
        }
    },

];
