<?php

namespace Hks\Schema\Toolkit;

use Hks\Schema\Data\Exceptions\TypeMismatchException;

class Assert
{
    /**
     * Throws unless every item is an instance of the given class.
     *
     * @param iterable<mixed> $items
     * @param class-string $type
     */
    public static function containsOnly(iterable $items, string $type): void
    {
        foreach ($items as $key => $item) {
            if (! $item instanceof $type) {
                throw TypeMismatchException::at($type, $item, $key);
            }
        }
    }
}
