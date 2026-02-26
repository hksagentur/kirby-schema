<?php

namespace Hks\Schema\Data\Concerns;

trait ComparesNumbers
{
    use ComparesValues;

    public static function min(self $value, self ...$others): static
    {
        $smallest = $value;

        foreach ($others as $other) {
            if ($other->isSmallerThan($smallest)) {
                $smallest = $other;
            }
        }

        return $smallest;
    }

    public static function max(self $value, self ...$others): static
    {
        $greatest = $value;

        foreach ($others as $other) {
            if ($other->isGreaterThan($greatest)) {
                $greatest = $other;
            }
        }

        return $greatest;
    }

    public function isGreaterThan(int|float|self $other): bool
    {
        return $this->compare(static::from($other)) > 0;
    }

    public function isGreaterThanOrEqualTo(int|float|self $other): bool
    {
        return $this->compare(static::from($other)) >= 0;
    }

    public function isSmallerThan(int|float|self $other): bool
    {
        return $this->compare(static::from($other)) < 0;
    }

    public function isSmallerThanOrEqualTo(int|float|self $other): bool
    {
        return $this->compare(static::from($other)) <= 0;
    }

    abstract public function compare(self $other): int;
}
