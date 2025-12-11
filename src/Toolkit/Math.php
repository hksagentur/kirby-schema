<?php

namespace Hks\Schema\Toolkit;

class Math
{
    public static function greatestCommonDivisor(int $a, int $b): int
    {
        return $b === 0 ? abs($a) : static::greatestCommonDivisor($b, $a % $b);
    }

    public static function leastCommonMultiple(int $a, int $b): int
    {
        if ($a === 0 || $b === 0) {
            return 0;
        }

        return abs($a * $b) / static::greatestCommonDivisor($a, $b);
    }
}
