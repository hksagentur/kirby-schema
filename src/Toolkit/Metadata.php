<?php

namespace Hks\Schema\Toolkit;

use Kirby\Data\Data;

class Metadata
{
    /** @return array<string, mixed> */
    public static function read(string $name): array
    {
        return Data::read(static::root() . "/resources/{$name}.php");
    }

    /**
     * @return array<string, array<string, string>>
     */
    public static function countryNames(): array
    {
        return static::read('country-names');
    }

    /**
     * The plugin's root, resolved from this file: `config/translations.php`
     * reads country and currency names while the plugin is still being
     * registered, before `App::plugin()` knows it.
     */
    protected static function root(): string
    {
        return dirname(__DIR__, 2);
    }
}
