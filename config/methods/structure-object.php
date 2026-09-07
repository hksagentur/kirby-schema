<?php

use Hks\Schema\Data\DataObject;
use Hks\Schema\Data\Exceptions\NotFoundException;
use Hks\Schema\Data\Exceptions\ValidationException;
use Hks\Schema\Data\Registry;

return [

    // Converters

    /**
     * Converts the structure object's content to a data object. Throws
     * for an unregistered name or content that fails to resolve.
     */
    'toDataObject' => function (string $name): DataObject {
        $class = Registry::dataObject($name);

        if (! $class) {
            throw NotFoundException::for('No data object is registered for "%s".', $name);
        }

        try {
            return $class::fromArray($this->toArray());
        } catch (Throwable $exception) {
            throw new ValidationException(
                message: sprintf('Invalid %s data for structure object "%s"', $name, $this->id()),
                previous: $exception,
            );
        }
    },

];
