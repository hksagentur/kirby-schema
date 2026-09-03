<?php

namespace Hks\Schema\Data\Types;

use Hks\Schema\Data\Contracts\Localizable;
use Kirby\Cms\App;
use Kirby\Toolkit\I18n;
use Kirby\Toolkit\Str;

enum Currency: string implements Localizable
{
    case USD = 'USD';
    case EUR = 'EUR';
    case JPY = 'JPY';
    case GBP = 'GBP';
    case CNY = 'CNY';
    case CHF = 'CHF';

    public static function of(string|self|null $value): ?static
    {
        if ($value instanceof self) {
            return $value;
        }

        return static::fromName((string) $value);
    }

    public static function fromName(string $name): ?static
    {
        return static::tryFrom(Str::upper($name));
    }

    public static function default(self $fallback = self::EUR): static
    {
        return static::of(
            App::instance()->option('hksagentur.schema.defaultCurrency', $fallback)
        ) ?? $fallback;
    }

    public function key(): string
    {
        return Str::lower($this->name);
    }

    public function name(): string
    {
        return $this->name;
    }

    public function label(): string
    {
        return $this->toLocaleString();
    }

    public function symbol(): string
    {
        return match ($this) {
            self::USD => '$',
            self::EUR => '€',
            self::JPY => '¥',
            self::GBP => '£',
            self::CNY => '¥',
            self::CHF => 'CHF',
        };
    }

    public function minorUnits(): int
    {
        return match ($this) {
            self::JPY => 0,
            default => 2,
        };
    }

    public function toString(): string
    {
        return $this->name();
    }

    public function toLocaleString(?string $locale = null): string
    {
        return I18n::translate("hksagentur.schema.currency.{$this->key()}", $this->name(), $locale);
    }
}
