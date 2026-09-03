<?php

use Hks\Schema\Data\DataObject;
use Hks\Schema\Data\Registry;

return [

    // Converters

    /**
     * Converts the content of the current structure object to the given data
     * transfer object. `$name` accepts either a registered alias
     * (`'person'`) or a `DataObject` class-string (`Person::class`).
     */
    'toDataObject' => function (string $name): ?DataObject {
        $class = Registry::dataObject($name);

        if (! $class) {
            return null;
        }

        try {
            return $class::fromArray($this->toArray());
        } catch (Throwable) {
            return null;
        }
    },

];
