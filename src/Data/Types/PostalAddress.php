<?php

namespace Hks\Schema\Data\Types;

use Closure;
use Hks\Schema\Data\Attributes\ArrayInput;
use Hks\Schema\Data\Contracts\Arrayable;
use Hks\Schema\Data\Contracts\Htmlable;
use Hks\Schema\Data\Contracts\Localizable;
use Hks\Schema\Data\Contracts\Schemable;
use Hks\Schema\Data\Exceptions\NotFoundException;
use Hks\Schema\Format\PostalAddressFormat;
use Hks\Schema\Toolkit\Str;
use InvalidArgumentException;
use Kirby\Toolkit\A;
use Kirby\Toolkit\Collection;
use Throwable;

#[ArrayInput]
readonly class PostalAddress extends DataType implements Arrayable, Htmlable, Localizable, Schemable
{
    public function __construct(
        public ?string $streetAddress = null,
        public ?string $postalCode = null,
        public ?string $locality = null,
        public ?string $region = null,
        public ?Country $country = null,
    ) {
        if (
            static::isBlank($streetAddress) &&
            static::isBlank($postalCode) &&
            static::isBlank($locality) &&
            static::isBlank($region) &&
            $country === null
        ) {
            throw new InvalidArgumentException(
                'A postal address must have at least one property set.'
            );
        }
    }

    public static function from(array|self $value): static
    {
        if ($value instanceof self) {
            return $value;
        }

        $value = array_filter(array_change_key_case($value));

        return new static(
            $value['streetaddress'] ?? null,
            $value['postalcode'] ?? null,
            $value['addresslocality'] ?? null,
            $value['addressregion'] ?? null,
            Country::tryFrom((string) ($value['addresscountry'] ?? '')),
        );
    }

    public static function tryFrom(array|self $value): ?static
    {
        try {
            return static::from($value);
        } catch (Throwable) {
            return null;
        }
    }

    public function isSameLocality(self $other): bool
    {
        return $this->locality() === $other->locality();
    }

    public function isSameCountry(self $other): bool
    {
        return $this->countryCode() === $other->countryCode();
    }

    public function isSameRegion(self $other): bool
    {
        return $this->region() === $other->region();
    }

    public function isInternational(): bool
    {
        return $this->countryCode() !== Country::default()->code();
    }

    public function isDomestic(): bool
    {
        return ! $this->isInternational();
    }

    public function hasStreetAddress(): bool
    {
        return $this->streetAddress !== null;
    }

    public function hasPostalCode(): bool
    {
        return $this->postalCode !== null;
    }

    public function hasLocality(): bool
    {
        return $this->locality !== null;
    }

    public function hasRegion(): bool
    {
        return $this->region !== null;
    }

    public function hasCountry(): bool
    {
        return $this->country !== null;
    }

    public function streetAddress(): ?string
    {
        return $this->streetAddress;
    }

    public function postalCode(): ?string
    {
        return $this->postalCode;
    }

    public function locality(): ?string
    {
        return $this->locality;
    }

    public function region(): ?string
    {
        return $this->region;
    }

    public function country(): ?Country
    {
        return $this->country;
    }

    public function countryCode(): string
    {
        return ($this->country ?? Country::default())->code();
    }

    public function countryName(?string $locale = null): string
    {
        return ($this->country ?? Country::default())->toLocaleString($locale);
    }

    /**
     * @param Closure(string $value, string $field): string $callback
     * @return Collection<int, string>
     */
    public function addressLines(Closure $callback, ?string $locale = null): Collection
    {
        $data = $this->templateData($locale);

        if (! $data) {
            return new Collection();
        }

        $data = array_combine(
            array_keys($data),
            array_map($callback, $data, array_keys($data)),
        );

        $addressFormat = $this->addressFormat()?->lines();

        if (! $addressFormat) {
            throw NotFoundException::for(
                'Missing a valid address format for country "%s".',
                $this->countryCode(),
            );
        }

        $lines = [];

        foreach ($addressFormat as $line) {
            if ($line = $this->formatAddressLine($line, $data)) {
                $lines[] = $line;
            }
        }

        return new Collection($lines);
    }

    public function equals(DataType $other): bool
    {
        if (! ($other instanceof self)) {
            return false;
        }

        return (
            $this->streetAddress === $other->streetAddress &&
            $this->postalCode === $other->postalCode &&
            $this->locality === $other->locality &&
            $this->region === $other->region &&
            $this->country?->code() === $other->country?->code()
        );
    }

    public function toString(): string
    {
        return $this->toLocaleString();
    }

    public function toLocaleString(?string $locale = null, array $options = []): string
    {
        return $this->addressLines(
            fn (string $value): string => Str::esc($value),
            $locale,
        )->join("\n");
    }

    public function toArray(): array
    {
        return [
            'streetAddress' => $this->streetAddress,
            'postalCode' => $this->postalCode,
            'addressLocality' => $this->locality,
            'addressRegion' => $this->region,
            'addressCountry' => $this->country?->code(),
        ];
    }

    public function toStructuredData(): array
    {
        return array_filter([
            '@type' => 'PostalAddress',
            ...$this->toArray(),
        ]);
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    protected function addressFormat(): ?PostalAddressFormat
    {
        return PostalAddressFormat::get($this->countryCode()) ?? PostalAddressFormat::default();
    }

    protected function templateData(?string $locale = null): array
    {
        $data = $this->toArray();

        if ($this->isDomestic()) {
            $data = A::without($data, 'addressCountry');
        } else {
            $data['addressCountry'] = $this->countryName($locale);
        }

        return array_filter($data);
    }

    /** @param array<string, string> $data */
    protected function formatAddressLine(string $line, array $data = []): string
    {
        return trim(Str::squish(Str::template($line, $data, ['fallback' => ''])), ' ,/');
    }

}
