<?php

namespace Hks\Schema\Data\Contracts;

use Countable;
use Generator;
use IteratorAggregate;
use JsonSerializable;

/**
 * @template TKey of array-key
 * @template-covariant TValue as object
 *
 * @extends Arrayable<TKey, TValue>
 * @extends IteratorAggregate<TKey, TValue>
 */
interface Enumerable extends Arrayable, Countable, IteratorAggregate, Jsonable, JsonSerializable
{
    /**
     * Determine whether the collection is empty.
     */
    public function isEmpty(): bool;

    /**
     * Determine whether the collection is not empty.
     */
    public function isNotEmpty(): bool;

    /**
     * Get the first item in the collection.
     *
     * @return ?TValue
     */
    public function first(): mixed;

    /**
     * Get the last item in the collection.
     *
     * @return ?TValue
     */
    public function last(): mixed;

    /**
     * Get the keys of the collection.
     *
     * @return array<int, TKey>
     */
    public function keys(): array;

    /**
     * Get the values of the collection.
     *
     * @return array<int, TValue>
     */
    public function values(): array;

    /**
     * Get all items of the collection.
     *
     * @return array<TKey, TValue>
     */
    public function all(): array;

    /**
     * Get the items of the collection in pairs.
     *
     * @return Generator<int, array{TValue, TValue}>
     */
    public function pairwise(): Generator;

    /**
     * Get the collection's iterator.
     *
     * @return Generator<TKey, TValue>
     */
    public function getIterator(): Generator;
}
