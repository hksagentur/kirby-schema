<?php

namespace Hks\Schema\Toolkit;

use CommerceGuys\Addressing\Country\CountryRepository;
use Kirby\Cms\App;

class Country
{
    /** @var array<string, array<string, \CommerceGuys\Addressing\Country\Country>  */
    protected static array $countries = [];

    /** @return array<string, \CommerceGuys\Addressing\Country\Country>  */
    protected static function all(string $locale = null): array
    {
        $locale ??= App::instance()
            ->language()
            ->locale(LC_ALL);

        return static::$countries[$locale] ??= (new CountryRepository($locale))->getAll($locale);
    }

    public static function nameFromCode(string $code, ?string $locale = null): ?string
    {
        return static::all($locale)[$code] ?? null;
    }

    public static function codeFromName(string $name, ?string $locale = null): ?string
    {
        foreach (static::all($locale) as $country) {
            if ($country->getName() === $name) {
                return $country->getCountryCode();
            }
        }

        return null;
    }
}
