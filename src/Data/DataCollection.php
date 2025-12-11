<?php

namespace Hks\Schema\Data;

use Closure;
use Kirby\Cms\Html;
use Kirby\Toolkit\A;
use Kirby\Toolkit\Iterator;
use Stringable;

/**
 * @template TValue of DataObject
 * @extends Iterator<int, TValue>
 */
class DataCollection extends Iterator implements Stringable
{
    /**
     * @param TValue[] $data
     */
    public function __construct(array $data = [])
	{
		$this->data = array_filter($data);
	}

    /**
     * @param TValue[] $data
     */
    public static function from(array $data): static
    {
        return new static($data);
    }

    public static function empty(): static
    {
        return new static();
    }

    public function isEmpty(): bool
    {
        return empty($this->data);
    }

    public function isNotEmpty(): bool
    {
        return ! $this->isNotEmpty();
    }

    /**
     * @return TValue[]
     */
    public function all(): array
    {
        return $this->data;
    }

    /**
     * @return ?TValue
     */
    public function first(): DataObject
    {
        return A::first($this->data);
    }

    /**
     * @return ?TValue
     */
    public function last(): DataObject
    {
        return A::last($this->data);
    }

    public function slice(int $offset = 0, ?int $limit = null): static
    {
        return new static(array_slice($this->data, $offset, $limit));
	}

    public function offset(int $offset): static
	{
		return $this->slice($offset);
	}

    public function limit(int $limit): static
	{
		return $this->slice(0, $limit);
	}

    /**
     * @template TMapValue
     *
     * @param \Closure(TValue): TMapValue $callback
     * @return TMapValue[]
     */
    public function map(Closure $callback): array
    {
        return array_map($callback, $this->data);
    }

    /**
     * @return mixed[]
     */
    public function pluck(string $value, ?string $key = null): array
    {
        $results = [];

        foreach ($this->data as $item) {
            if (is_null($key)) {
                $itemValue = $item->{$value} ?? null;
                $results[] = $itemValue;
            } else {
                $itemKey = $item->{$key} ?? null;
                $itemValue = $item->{$value} ?? null;

                $results[(string) $itemKey] = $itemValue;
            }
        }

        return $results;
    }

    public function toString(): string
    {
        return $this->toHtml();
    }

    public function toJson(): string
	{
		return json_encode($this->toArray());
	}

    public function toHtml(array $attributes = []): string
    {
        $content = $this->toArray(fn (DataObject $item) => $item->toHtml());

        if (empty($content)) {
            return '';
        }

        return Html::tag('div', $content, $attributes);
    }

    /**
     * @template TMapValue
     *
     * @param \Closure(TValue): TMapValue $callback
     * @return TMapValue[]
     */
    public function toArray(?Closure $callback = null): array
    {
        return $this->map($callback ?? fn (DataObject $object) => $object->toArray());
    }

    /**
     * @template TWhenReturnType
     *
     * @param Closure($this): TWhenReturnType $callback
     * @param ?Closure($this): TWhenReturnType $fallback
     * @return $this|TWhenReturnType
     */
    public function when(mixed $value = null, Closure $callback = null, Closure $fallback = null): mixed
    {
        $value = $value instanceof Closure ? $value($this) : $value;

        if ($value) {
            return $callback($this, $value) ?? $this;
        } elseif ($fallback) {
            return $fallback($this, $value) ?? $this;
        }

        return $this;
    }

    /**
     * @template TWhenEmptyReturnType
     *
     * @param Closure($this): TWhenEmptyReturnType $callback
     * @param ?Closure($this): TWhenEmptyReturnType $fallback
     * @return $this|TWhenEmptyReturnType
     */
    public function whenEmpty(Closure $callback, ?Closure $fallback = null): mixed
    {
        return $this->when($this->isEmpty(), $callback, $fallback);
    }

    /**
     * @template TUnlessEmptyReturnType
     *
     * @param Closure($this): TUnlessEmptyReturnType $callback
     * @param ?Closure($this): TUnlessEmptyReturnType $fallback
     * @return $this|TUnlessEmptyReturnType
     */
    public function unlessEmpty(Closure $callback, ?Closure $fallback = null): mixed
    {
        return $this->when($this->isNotEmpty(), $callback, $fallback);
    }

    public function __toString(): string
    {
        return $this->toString();
    }
}
