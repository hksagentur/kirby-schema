<?php

namespace Hks\Schema\Format;

use Kirby\Cms\App;
use Kirby\Data\Data;
use Kirby\Toolkit\A;

readonly class PhoneNumberFormat extends Format
{
    public function __construct(
        public string $countryCode,
        public string $callingCode,
        public ?string $trunkPrefix = null,
    ) {
    }

    public static function byCallingCode(string $callingCode): ?static
    {
        foreach (static::all() as $format) {
            if ($format->callingCode === $callingCode) {
                return $format;
            }
        }

        return null;
    }

    public function callingCode(): string
    {
        return $this->callingCode;
    }

    public function trunkPrefix(): ?string
    {
        return $this->trunkPrefix;
    }

    /**
     * @return array<string, array{callingCode: string, trunkPrefix: ?string}>
     */
    public static function load(): array
    {
        $formats = A::merge(
            Data::read(static::directory() . '/calling-codes.php'),
            App::instance()->option('hksagentur.schema.phoneNumberFormats', []),
            A::MERGE_REPLACE,
        );

        $props = [];

        foreach ($formats as $code => $format) {
            if (! empty($format['callingCode'])) {
                $props[$code] = A::get($format, ['callingCode', 'trunkPrefix']);
            }
        }

        return $props;
    }
}
