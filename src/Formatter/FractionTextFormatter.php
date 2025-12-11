<?php

namespace Hks\Schema\Formatter;

use Hks\Schema\Data\Fraction;

/**
 * @extends Formatter<Fraction>
 */
class FractionTextFormatter extends Formatter
{
    public function format(mixed $fraction): string
    {
        [$numerator, $denominator] = $fraction->toArray();

        if ($denominator === 1) {
            return (string) $numerator;
        }

        return "{$numerator}/{$denominator}";
    }
}
