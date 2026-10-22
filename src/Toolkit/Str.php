<?php

namespace Hks\Schema\Toolkit;

class Str extends \Kirby\Toolkit\Str
{
    /**
     * Collapses whitespace to single spaces and trims the ends.
     *
     * Trimming also removes invisible format characters such as zero-width spaces.
     */
    public static function squish(string $string): string
    {
        $string = preg_replace('/\s+/u', ' ', $string);
        $string = preg_replace('/^[\s\p{Cf}]+|[\s\p{Cf}]+$/u', '', $string);

        return $string;
    }

    /**
     * Collapses repeated occurrences of the given characters into one.
     *
     * @param string|string[] $characters
     */
    public static function deduplicate(string $string, string|array $characters = ' '): string
    {
        if (is_string($characters)) {
            return preg_replace('/' . preg_quote($characters, '/') . '+/u', $characters, $string);
        }

        return array_reduce($characters, static::deduplicate(...), $string);
    }
}
