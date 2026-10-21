<?php

namespace Hks\Schema\Format;

use Kirby\Cms\App;
use Kirby\Data\Data;
use Kirby\Toolkit\A;

readonly class PostalAddressFormat extends Format
{
    /** @param string[] $lines */
    public function __construct(
        public string $countryCode,
        public array $lines,
    ) {
    }

    /**
     * @return string[]
     */
    public function lines(): array
    {
        return $this->lines;
    }

    /**
     * @return array<string, array{lines: string[]}>
     */
    public static function load(): array
    {
        $directory = static::directory();

        $schemes = Data::read($directory . '/address-schemes.php');
        $formats = Data::read($directory . '/address-formats.php');

        $formats = A::merge(
            $formats,
            App::instance()->option('hksagentur.schema.addressFormats', []),
            A::MERGE_REPLACE,
        );

        $data = [];

        foreach ($formats as $code => $format) {
            $lines = match (true) {
                is_string($format) => $schemes[$format] ?? null,
                is_array($format) => $format,
                default => null,
            };

            if (! empty($lines)) {
                $data[$code] = ['lines' => $lines];
            }
        }

        return $data;
    }
}
