<?php

namespace Hks\Schema\Data\Concerns;

trait ComparesNumbers
{
    use ComparesValues;

    public static function min(self $value, self ...$others): static
    {
        $smallest = $value;

        foreach ($others as $other) {
            if ($other->isLessThan($smallest)) {
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

    public function isGreaterThan(mixed $other): bool
    {
        return $this->compare($other) > 0;
    }

    public function isGreaterThanOrEqualTo(mixed $other): bool
    {
        return $this->compare($other) >= 0;
    }

    public function isLessThan(mixed $other): bool
    {
        return $this->compare($other) < 0;
    }

    public function isLessThanOrEqualTo(mixed $other): bool
    {
        return $this->compare($other) <= 0;
    }

    abstract public function compare(mixed $other): int;
}
