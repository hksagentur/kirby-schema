<?php

namespace Hks\Schema\Toolkit;

use Hks\Schema\Data\Exceptions\TypeMismatchException;

class Assert
{
    /**
     * Assert that the given iterable contains only items of the specified type.
     *
     * @param iterable $items
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
