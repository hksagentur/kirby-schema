<?php

namespace Hks\Schema\Data\Types;

use Hks\Schema\Data\Contracts\Localizable;
use Hks\Schema\Data\Exceptions\NotFoundException;
use Hks\Schema\Toolkit\Metadata;
use InvalidArgumentException;
use Kirby\Cms\App;
use Kirby\Toolkit\Collection;
use Kirby\Toolkit\I18n;
use Kirby\Toolkit\Str;

readonly class Country extends StringType implements Localizable
{
    public const string ISO_COUNTRY_CODE = '/^[A-Z]{2}$/';

    public function __construct(string $code)
    {
        $code = Str::upper($code);

        if (! Str::matches($code, self::ISO_COUNTRY_CODE)) {
            throw new InvalidArgumentException(sprintf(
                'The given value "%s" does not represent an ISO 3166-1 alpha-2 country code.',
                $code,
            ));
        }

        parent::__construct($code);
    }

    public static function default(): static
    {
        $country = static::tryFrom(
            (string) App::instance()->option('hksagentur.schema.defaultCountry')
        );

        if (! $country) {
            throw NotFoundException::for(
                'The default country "%s" is not supported.',
                (string) App::instance()->option('hksagentur.schema.defaultCountry'),
            );
        }

        return $country;
    }

    /** @return Collection<string, static> */
    public static function all(?string $locale = null): Collection
    {
        $countries = [];

        foreach (static::codes() as $code) {
            $countries[$code] = static::from($code);
        }

        uasort($countries, fn (self $a, self $b) => strnatcasecmp(
            Str::ascii($a->toLocaleString($locale)),
            Str::ascii($b->toLocaleString($locale)),
        ));

        return new Collection($countries);
    }

    public function code(): string
    {
        return $this->value;
    }

    public function toLocaleString(?string $locale = null, array $options = []): string
    {
        return I18n::translate("hksagentur.schema.country.{$this->value}", $this->value, $locale);
    }

    /** @return string[] */
    protected static function codes(): array
    {
        return array_keys(array_merge(...array_values(
            Metadata::countryNames()
        )));
    }
}
