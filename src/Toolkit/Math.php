<?php

namespace Hks\Schema\Toolkit;

class Math
{
    /**
     * Returns the largest integer that divides both numbers.
     */
    public static function greatestCommonDivisor(int $a, int $b): int
    {
        return $b === 0 ? abs($a) : static::greatestCommonDivisor($b, $a % $b);
    }

    /**
     * Returns the smallest positive integer divisible by both numbers.
     */
    public static function leastCommonMultiple(int $a, int $b): int
    {
        if ($a === 0 || $b === 0) {
            return 0;
        }

        return intdiv(abs($a * $b), static::greatestCommonDivisor($a, $b));
    }
}
