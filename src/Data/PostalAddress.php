<?php

namespace Hks\Schema\Data;

use Hks\Schema\Formatter\PersonSchemaTransformer;
use Hks\Schema\Formatter\PostalAddressHtmlFormatter;
use Hks\Schema\Formatter\PostalAddressTextFormatter;
use InvalidArgumentException;

readonly class PostalAddress extends CompositeDataValue
{
    public function __construct(
        public ?Text $streetAddress,
        public ?Text $postalCode,
        public ?Text $locality,
        public ?Country $country,
    ) {
    }

    public static function from(string ...$values): static
    {
        return new static(...$values);
    }

    public static function tryFrom(string ...$values): ?static
    {
        try {
            return !empty($values) ? static::from(...$values) : null;
        } catch (InvalidArgumentException) {
            return null;
        }
    }

    public static function fromArray(array $attributes): static
    {
        return new static(
            streetAddress: Text::tryFrom($attributes['streetAddress'] ?? null),
            postalCode: Text::tryFrom($attributes['postalCode'] ?? null),
            locality: Text::tryFrom($attributes['locality'] ?? null),
            country: Country::tryFrom($attributes['country'] ?? null),
        );
    }

    public function streetAddress(): ?Text
    {
        return $this->streetAddress;
    }

    public function postalCode(): ?Text
    {
        return $this->postalCode;
    }

    public function locality(): ?Text
    {
        return $this->locality;
    }

    public function country(): ?Country
    {
        return $this->country;
    }

    public function toArray(): array
    {
        return [
            'streetAddress' => $this->streetAddress?->toString(),
            'postalCode' => $this->postalCode?->toString(),
            'locality' => $this->locality?->toString(),
            'country' => $this->country?->toString(),
        ];
    }

    public function toString(): string
    {
        return $this->format(PostalAddressTextFormatter::class);
    }

    public function toHtml(array $attributes = []): string
    {
        return $this->format(PostalAddressHtmlFormatter::class, ['attributes' => $attributes]);
    }

    public function toSchema(): array
    {
        return $this->transform(PersonSchemaTransformer::class);
    }
}
