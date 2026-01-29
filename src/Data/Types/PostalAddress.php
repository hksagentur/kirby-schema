<?php

namespace Hks\Schema\Data\Types;

use Hks\Schema\Data\Contracts\Arrayable;
use Hks\Schema\Data\Contracts\Localizable;
use InvalidArgumentException;
use Kirby\Cms\App;
use Kirby\Toolkit\I18n;
use Kirby\Toolkit\Str;
use RuntimeException;
use Throwable;

readonly class PostalAddress extends DataType implements Arrayable, Localizable
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
        return ($value instanceof self) ? $value : new static(
            $value['streetAddress'] ?? null,
            $value['postalCode'] ?? null,
            $value['addressLocality'] ?? null,
            $value['addressCountry'] ?? null,
            $value['addressRegion'] ?? null,
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

    public function hasCountryCode(): bool
    {
        return $this->countryCode() !== null;
    }

    public function streetAdress(): ?string
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

    public function format(string $mode = 'text', ?string $locale = null): string
    {
        $addressFormat = static::addressFormat($this->countryCode());

        if (! $addressFormat) {
            throw new RuntimeException(sprintf(
                'Missing a valid address format for country "%s".',
                $this->countryCode(),
            ));
        }

        $addressData = $this->getLocalizedAddressData($locale);

        // Hide the country name from domestic addresses
        if ($this->isDomestic()) {
            unset($addressData['addressCountry']);
        }

        $addressFields = match ($mode) {
            'html' => $this->renderAddressFields($addressData),
            default => array_filter($addressData),
        };

        if (! $addressFields) {
            return '';
        }

        $addressLines = $this->renderAddressLines($addressFormat, $addressFields);

        if (! $addressLines) {
            return '';
        }

        return match ($mode) {
            'html' => Html::breaks(implode("\n", $addressLines)),
            default => implode("\n", $addressLines),
        };
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

    public function toHtml(array $attributes = []): string
    {
        return Html::tag('div', [$this->format('html')], [
            'itemscope' => true,
            'itemtype' => 'https://schema.org/PostalAddress',
            'translate' => 'no',
            ...$attributes,
        ]);
    }

    public function toString(): string
    {
        return $this->format('text');
    }

    public function toLocaleString(?string $locale = null): string
    {
        return $this->format('text', $locale);
    }

    protected function getLocalizedAddressData(?string $locale = null): array
    {
        $data = $this->toArray();

        if ($countryName = $this->countryName($locale)) {
            $data['addressCountry'] = $countryName;
        }

        return $data;
    }

    protected function renderAddressFields(array $data): array
    {
        $fields = array_filter($data);

        $fields = array_map(function ($value, $field) {
            return Html::tag('span', $value, ['itemprop' => $field]);
        }, $fields, array_keys($fields));

        return $fields;
    }

    protected function renderAddressLines(array $lines, array $data): array
    {
        $lines = array_map(function (string $line) use ($data) {
            return Str::trim(Str::template($line, $data, ['fallback' => '']), ', ');
        }, $lines);

        return array_filter($lines);
    }
}
