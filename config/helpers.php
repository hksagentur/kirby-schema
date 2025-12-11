<?php

use Hks\Schema\Formatter\Manager;
use Hks\Schema\Formatter\Formatter;

if (! function_exists('blank')) {
    /**
     * Determine if the given value is "blank" or empty.
     */
    function blank(mixed $value): bool
    {
        if (is_null($value)) {
            return true;
        }

        if (is_string($value)) {
            return trim($value) === '';
        }

        if (is_numeric($value) || is_bool($value)) {
            return false;
        }

        if ($value instanceof Countable) {
            return count($value) === 0;
        }

        if ($value instanceof Stringable) {
            return trim((string) $value) === '';
        }

        return empty($value);
    }
}

if (! function_exists('format')) {
    /**
     * Format a value with a given formatter.
     *
     * @template TFormatter of Formatter
     *
     * @param string|class-string<TFormatter> $formatter
     * @param mixed $value
     * @param array $options
     * @return string
     */
    function format(string $formatter, mixed $value, array $options = []): string
    {
        return Manager::instance()->format($formatter, $value, $options);
    }
}
