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
            ? new static($value->all())
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
            ? $value->all()
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
        return count($this->all());
    }

    /** @return ?TValue */
    public function first(): mixed
    {
        return A::first($this->all());
    }

    /** @return ?TValue */
    public function last(): mixed
    {
        return A::last($this->all());
    }

    /** @return array<int, TKey> */
    public function keys(): array
    {
        return array_keys($this->all());
    }

    /** @return array<int, TValue> */
    public function values(): array
    {
        return array_values($this->all());
    }

    /**
     * @template TMapValue
     * @param callable(TValue, TKey): TMapValue $callback
     * @return array<TKey, TMapValue>
     */
    public function map(callable $callback): array
    {
        $result = [];

        foreach ($this->all() as $key => $value) {
            $result[$key] = $callback($value, $key);
        }

        return $result;
    }

    /** @return Generator<int, array{TValue, TValue}> */
    public function pairwise(): Generator
    {
        $previous = null;

        foreach ($this->all() as $value) {
            if ($previous !== null) {
                yield [$previous, $value];
            }

            $previous = $value;
        }
    }

    /** @return array<TKey, TValue> */
    abstract public function all(): array;

    /** @return array<TKey, TValue> */
    public function toArray(): array
    {
        return $this->all();
    }

    public function toJson(int $options = 0): string|false
    {
        return json_encode($this, $options);
    }

    /** @return array<TKey, TValue> */
    public function jsonSerialize(): array
    {
        return $this->all();
    }

    /** @return Generator<TKey, TValue> */
    public function getIterator(): Generator
    {
        yield from $this->all();
    }

    /**
     * @param callable(TValue, TKey): bool $callback
     * @return ?TValue
     */
    protected function findBy(callable $callback): mixed
    {
        foreach ($this->all() as $key => $value) {
            if ($callback($value, $key)) {
                return $value;
            }
        }

        return null;
    }

    /**
     * @param callable(TValue, TKey): bool $callback
     * @return static<TKey, TValue>
     */
    protected function filterBy(callable $callback): static
    {
        return new static(array_filter($this->all(), $callback, ARRAY_FILTER_USE_BOTH));
    }
}
