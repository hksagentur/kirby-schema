<?php

namespace Hks\Schema\Format;

use Hks\Schema\Toolkit\Str;
use Kirby\Cms\App;
use Kirby\Toolkit\A;

abstract readonly class Format
{
    public string $countryCode;

    /**
     * @return array<string, array<string, mixed>>
     */
    abstract public static function load(): array;

    /**
     * @param array<string, mixed> $props
     */
    public static function factory(array $props): static
    {
        return new static(...$props);
    }

    /**
     * @return array<string, static>
     */
    public static function all(): array
    {
        $formats = [];

        foreach (static::load() as $code => $props) {
            $formats[$code] = static::factory(['countryCode' => $code, ...$props]);
        }

        return $formats;
    }

    public static function default(): ?static
    {
        return static::get(App::instance()->option('hksagentur.schema.defaultCountry'));
    }

    public static function get(?string $code = null): ?static
    {
        if ($code === null) {
            return static::default();
        }

        return A::get(static::all(), Str::upper($code));
    }

    public function countryCode(): string
    {
        return $this->countryCode;
    }

    protected static function directory(): string
    {
        return App::plugin('hksagentur/schema')->root() . '/resources';
    }
}
