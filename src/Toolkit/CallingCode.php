<?php

namespace Hks\Schema\Toolkit;

use Hks\Schema\Data\Types\Country;

readonly class CallingCode
{
    public function __construct(
        public string $code,
        public ?string $nationalPrefix = null,
    ) {
    }

    public static function for(Country $country): ?static
    {
        $items = Metadata::callingCodes();

        if (! $items) {
            return null;
        }

        $metadata = $items[$country->code()] ?? null;

        if (! $metadata) {
            return null;
        }

        return new static(
            $metadata['callingCode'],
            $metadata['nationalPrefix'],
        );
    }

    public static function find(string $code): ?static
    {
        foreach (Metadata::callingCodes() as $metadata) {
            if ($metadata['callingCode'] === $code) {
                return new static(
                    $metadata['callingCode'],
                    $metadata['nationalPrefix'],
                );
            }
        }

        return null;
    }

    public function code(): string
    {
        return $this->code;
    }

    public function nationalPrefix(): ?string
    {
        return $this->nationalPrefix;
    }

    public function toArray(): array
    {
        return [
            'callingCode' => $this->code,
            'nationalPrefix' => $this->nationalPrefix,
        ];
    }
}
