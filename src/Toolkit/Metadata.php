<?php

namespace Hks\Schema\Toolkit;

use Kirby\Cms\App;
use Kirby\Data\Data;
use Kirby\Toolkit\A;

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
     * @return array<string, array<string, string>>
     */
    public static function currencyNames(): array
    {
        return static::read('currency-names');
    }

    /**
     * @return array<string, array{callingCode: string, nationalPrefix: ?string}>
     */
    public static function callingCodes(): array
    {
        $entries = A::merge(
            static::read('calling-codes'),
            static::option('callingCodes', []),
            A::MERGE_REPLACE,
        );

        $callingCodes = [];

        foreach ($entries as $code => $entry) {
            if (! empty($entry['callingCode'])) {
                $callingCodes[$code] = [
                    'callingCode' => $entry['callingCode'],
                    'nationalPrefix' => $entry['nationalPrefix'] ?? null,
                ];
            }
        }

        return $callingCodes;
    }

    /**
     * @return array<string, string[]>
     */
    public static function addressFormats(): array
    {
        $schemes = static::read('address-schemes');

        $formats = A::merge(
            static::read('address-formats'),
            static::option('addressFormats', []),
            A::MERGE_REPLACE,
        );

        $lines = [];

        foreach ($formats as $code => $format) {
            $format = is_string($format) ? ($schemes[$format] ?? null) : $format;

            if (is_array($format) && ! empty($format)) {
                $lines[$code] = $format;
            }
        }

        return $lines;
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

    /** @return array<string, mixed> */
    protected static function option(string $key, ?array $default = null): mixed
    {
        return App::instance()->option("hksagentur.schema.{$key}", $default);
    }
}
