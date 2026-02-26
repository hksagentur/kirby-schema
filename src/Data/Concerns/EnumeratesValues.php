<?php

namespace Hks\Schema\Data\Concerns;

use Generator;
use Hks\Schema\Data\Contracts\Enumerable;
use Kirby\Toolkit\A;

/**
 * @template TKey of array-key
 * @template-covariant TValue as object
 */
trait EnumeratesValues
{
    /**
     * @template TWrapValue
     *
     * @param  array<array-key, TWrapValue>|TWrapValue $value
     * @return static<array-key, TWrapValue>
     */
    public static function wrap(array|Enumerable $value): static
    {
        return $value instanceof Enumerable
            ? new static($value->toArray())
            : new static($value);
    }

    /**
     * @template TUnwrapKey of array-key
     * @template TUnwrapValue
     *
     * @param  array<TUnwrapKey, TUnwrapValue>|static<TUnwrapKey, TUnwrapValue>  $value
     * @return array<TUnwrapKey, TUnwrapValue>
     */
    public static function unwrap(array|Enumerable $value): array
    {
        return $value instanceof Enumerable
            ? $value->toArray()
            : $value;
    }

    public function isEmpty(): bool
    {
        return $this->count() === 0;
    }

    public function isNotEmpty(): bool
    {
        return !$this->isEmpty();
    }

    public function count(): int
    {
        return count($this->getEnumerableValues());
    }

    /** @return ?TValue */
    public function first(): mixed
    {
        return A::first($this->getEnumerableValues());
    }

    /** @return ?TValue */
    public function last(): mixed
    {
        return A::last($this->getEnumerableValues());
    }

    /** @return array<int, TKey> */
    public function keys(): array
    {
        return array_keys($this->getEnumerableValues());
    }

    /** @return array<int, TValue> */
    public function values(): array
    {
        return array_values($this->getEnumerableValues());
    }

    /** @return Generator<int, array{TValue, TValue}> */
    public function pairwise(): Generator
    {
        $previous = null;

        foreach ($this->getEnumerableValues() as $value) {
            if ($previous !== null) {
                yield [$previous, $value];
            }

            $previous = $value;
        }
    }

    /** @return array<TKey, TValue> */
    public function all(): array
    {
        return $this->getEnumerableValues();
    }

    /** @return array<TKey, TValue> */
    abstract public function toArray(): array;

    public function toJson(int $options = 0): string|false
    {
        return json_encode($this, $options);
    }

    /** @return array<TKey, TValue> */
    public function jsonSerialize(): array
    {
        return $this->getEnumerableValues();
    }

    /** @return Generator<TKey, TValue> */
    public function getIterator(): Generator
    {
        yield from $this->getEnumerableValues();
    }

    /** @return array<TKey, TValue> */
    protected function getEnumerableValues(): array
    {
        return $this->toArray();
    }
}
