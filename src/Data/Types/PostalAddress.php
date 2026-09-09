<?php

namespace Hks\Schema\Data\Types;

use Closure;
use Hks\Schema\Data\Attributes\ArrayInput;
use Hks\Schema\Data\Contracts\Arrayable;
use Hks\Schema\Data\Contracts\Htmlable;
use Hks\Schema\Data\Contracts\Localizable;
use Hks\Schema\Data\Contracts\Schemable;
use Hks\Schema\Data\Exceptions\NotFoundException;
use InvalidArgumentException;
use Kirby\Cms\App;
use Kirby\Toolkit\A;
use Kirby\Toolkit\Collection;
use Kirby\Toolkit\I18n;
use Kirby\Toolkit\Str;
use Throwable;

#[ArrayInput]
readonly class PostalAddress extends DataType implements Arrayable, Htmlable, Localizable, Schemable
{
    public const string ISO_COUNTRY_CODE = '/^[A-Z]{2,3}$/';

    public const string DEFAULT_COUNTRY = 'DE';
    public const string DEFAULT_SCHEME = 'postalCodeLocality';

    public function __construct(
        public ?string $streetAddress = null,
        public ?string $postalCode = null,
        public ?string $locality = null,
        public ?string $country = null,
        public ?string $region = null,
    ) {
        if (
            ! isset($streetAddress) &&
            ! isset($postalCode) &&
            ! isset($locality) &&
            ! isset($country) &&
            ! isset($region)
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

        $value = array_change_key_case($value);

        return new static(
            $value['streetaddress'] ?? null,
            $value['postalcode'] ?? null,
            $value['addresslocality'] ?? null,
            $value['addresscountry'] ?? null,
            $value['addressregion'] ?? null,
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

    public static function defaultCountry(): string
    {
        return App::instance()->option('hksagentur.schema.defaultCountry', static::DEFAULT_COUNTRY);
    }

    /** @return array<string, string[]> */
    public static function addressSchemes(): array
    {
        return App::instance()->option('hksagentur.schema.addressSchemes', []);
    }

    /** @return ?string[] */
    public static function addressScheme(string $name): ?array
    {
        return static::addressSchemes()[$name] ?? null;
    }

    /** @return array<string, string[]> */
    public static function addressFormats(): array
    {
        return App::instance()->option('hksagentur.schema.addressFormats', []);
    }

    /** @return ?string[] */
    public static function addressFormat(string $countryCode): ?array
    {
        $formats = static::addressFormats();
        $defaultCountry = static::defaultCountry();

        $format = $formats[$countryCode] ?? $formats[$defaultCountry] ?? null;

        if (! is_array($format)) {
            return static::addressScheme($format ?? static::DEFAULT_SCHEME);
        }

        return $format;
    }

    public function isInternational(): bool
    {
        return $this->countryCode() !== static::defaultCountry();
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

    public function country(): ?string
    {
        return $this->country;
    }

    public function countryCode(): string
    {
        $value = $this->country();

        if (! $value || ! Str::matches($value, self::ISO_COUNTRY_CODE)) {
            return static::defaultCountry();
        }

        return Str::upper($value);
    }

    public function countryName(?string $locale = null): ?string
    {
        $countryCode = $this->countryCode();

        if (! $countryCode) {
            return $this->country();
        }

        return I18n::translate("hksagentur.schema.country.{$countryCode}", $this->country(), $locale);
    }

    /**
     * @param Closure(string $value, string $field): string $callback
     * @return Collection<int, string>
     */
    public function addressLines(Closure $callback, ?string $locale = null): Collection
    {
        $templateData = $this->templateFields($locale);

        if (! $templateData) {
            return new Collection();
        }

        $templateData = array_combine(
            array_keys($templateData),
            array_map($callback, $templateData, array_keys($templateData)),
        );

        $formattedLines = [];

        foreach ($this->addressFormatLines() as $addressFormatLine) {
            if ($formattedLine = Str::trim(Str::template($addressFormatLine, $templateData, ['fallback' => '']), ', ')) {
                $formattedLines[] = $formattedLine;
            }
        }

        return new Collection($formattedLines);
    }

    /** @return string[] */
    public function addressFormatLines(): array
    {
        $addressFormat = static::addressFormat($this->countryCode());

        if (! $addressFormat) {
            throw NotFoundException::for(
                'Missing a valid address format for country "%s".',
                $this->countryCode(),
            );
        }

        return $addressFormat;
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
            $this->country === $other->country &&
            $this->region === $other->region
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
            'addressCountry' => $this->country,
        ];
    }

    public function toStructuredData(): array
    {
        return [
            '@type' => 'PostalAddress',
            ...$this->toArray(),
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    protected function getTemplateData(?string $locale = null): array
    {
        $addressData = $this->toArray();

        if ($countryName = $this->countryName($locale)) {
            $addressData['addressCountry'] = $countryName;
        }

        return array_filter($addressData);
    }

    protected function templateFields(?string $locale = null): array
    {
        $templateData = $this->getTemplateData($locale);

        if ($this->isDomestic()) {
            $templateData = A::without($templateData, ['addressCountry']);
        }

        return $templateData;
    }
}
