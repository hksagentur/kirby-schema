<?php

namespace Hks\Schema\Toolkit;

use Hks\Schema\Data\Types\Country;

readonly class AddressFormat
{
    /** @param string[] $lines */
    public function __construct(
        public array $lines,
        public Country $country,
    ) {
    }

    public static function for(Country $country): ?static
    {
        $items = Metadata::addressFormats();

        if (! $items) {
            return null;
        }

        $lines = $items[$country->code()] ?? null;

        if (! $lines) {
            return null;
        }

        return new static($lines, $country);
    }

    /** @return string[] */
    public function lines(): array
    {
        return $this->lines;
    }

    public function country(): Country
    {
        return $this->country;
    }

    public function toArray(): array
    {
        return [
            'lines' => $this->lines,
            'country' => $this->country->code(),
        ];
    }
}
