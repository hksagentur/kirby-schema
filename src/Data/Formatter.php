<?php

namespace Hks\Schema\Data;

use Kirby\Cms\App;

abstract class Formatter
{
    protected static ?string $locale = null;

    public static function defaultLocale(): string
    {
        return static::$locale ?? App::instance()->language()->locale(LC_ALL);
    }

    public static function useLocale(string $locale): void
    {
        static::$locale = $locale;
    }

    public static function withLocale(string $locale, callable $callback): void
    {
        $previousLocale = static::defaultLocale();

        static::useLocale($locale);

        $callback();

        static::useLocale($previousLocale);
    }
}
