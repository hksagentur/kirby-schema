<?php

namespace Hks\Schema\Data\Concerns;

trait ComparesMoments
{
    use ComparesValues;

    public function isBefore(int|string|self $other): bool
    {
        return $this->compare(static::from($other)) < 0;
    }

    public function isBeforeOrEqualTo(int|string|self $other): bool
    {
        return $this->compare(static::from($other)) <= 0;
    }

    public function isAfter(int|string|self $other): bool
    {
        return $this->compare(static::from($other)) > 0;
    }

    public function isAfterOrEqualTo(int|string|self $other): bool
    {
        return $this->compare(static::from($other)) >= 0;
    }

    public function isBetween(int|string|self $start, int|string|self $end): bool
    {
        return $this->isAfterOrEqualTo($start) && $this->isBeforeOrEqualTo($end);
    }

    public function min(int|string|self $other): self
    {
        return $this->isBeforeOrEqualTo($other) ? $this : static::from($other);
    }

    public function max(int|string|self $other): self
    {
        return $this->isAfterOrEqualTo($other) ? $this : static::from($other);
    }

    abstract public function compare(self $other): int;
}
