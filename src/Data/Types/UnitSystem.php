<?php

namespace Hks\Schema\Data\Types;

use Hks\Schema\Data\Contracts\Localizable;
use Kirby\Toolkit\I18n;
use Kirby\Toolkit\Str;

enum UnitSystem implements Localizable
{
    case Imperial;
    case Metric;

    public static function fromLocale(?string $locale = null): static
    {
        return match ($locale ?? I18n::locale()) {
            'en_US', 'en_LR', 'en_MM' => self::Imperial,
            default => self::Metric,
        };
    }

    public function key(): string
    {
        return Str::lower($this->name);
    }

    public function name(): string
    {
        return $this->name;
    }

    public function toString(): string
    {
        return $this->name();
    }

    public function toLocaleString(?string $locale = null): string
    {
        return I18n::translate("hksagentur.schema.unitSystem.{$this->key()}", $this->name(), $locale);
    }
}
