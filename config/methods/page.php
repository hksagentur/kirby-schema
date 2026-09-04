<?php

use Hks\Schema\Data\DataObject;
use Hks\Schema\Data\Registry;

return [

    // Converters

    /**
     * Converts the page's content to a data object.
     */
    'toDataObject' => function (string $name): ?DataObject {
        $class = Registry::dataObject($name);

        if (! $class) {
            return null;
        }

        // `Content`'s constructor lowercases every top-level field key (`toArray()`
        // returns `givenname`, not `givenName`). Keep the full (lowercase) content —
        // it's the only source for anything the blueprint doesn't declare — but add
        // back the blueprint's own casing for every field it does declare, so
        // `fromArray()` can read camelCase keys like everywhere else in this codebase.
        $value = $this->content()->toArray();

        foreach (array_keys($this->blueprint()->fields()) as $field) {
            $lower = strtolower($field);

            if (array_key_exists($lower, $value)) {
                $value[$field] = $value[$lower];
            }
        }

        try {
            return $class::fromArray($value);
        } catch (Throwable) {
            return null;
        }
    },

];
