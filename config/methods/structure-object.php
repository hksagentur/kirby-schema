<?php

use Hks\Schema\Data\DataObject;
use Hks\Schema\Data\Registry;

return [

    // Converters

    /**
     * Converts the structure object's content to a data object.
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
