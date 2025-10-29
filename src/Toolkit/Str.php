<?php

namespace Hks\Schema\Toolkit;

class Str extends \Kirby\Toolkit\Str
{
    public static function deduplicate(string $string, string|array $characters = ' '): string
    {
        if (is_string($characters)) {
            return preg_replace('/' . preg_quote($characters, '/') . '+/u', $characters, $string);
        }

        return array_reduce($characters, static::deduplicate(...), $string);
    }
}
