<?php

namespace Hks\Schema\Data\Types;

use Hks\Schema\Data\Contracts\Localizable;
use Hks\Schema\Toolkit\I18n;
use Hks\Schema\Toolkit\Str;
use Stringable;

enum CardinalDirection implements Localizable, Stringable
{
    case North;
    case South;
    case East;
    case West;

    public function isLatitude(): bool
    {
        return in_array($this, [self::North, self::South]);
    }

    public function isLongitude(): bool
    {
        return in_array($this, [self::East, self::West]);
    }

    public function key(): string
    {
        return Str::lower($this->name);
    }

    public function name(): string
    {
        return $this->name;
    }

    public function opposite(): self
    {
        return match ($this) {
            self::North => self::South,
            self::South => self::North,
            self::East => self::West,
            self::West => self::East,
        };
    }

    public function toString(): string
    {
        return $this->name();
    }

    public function toLocaleString(?string $locale = null): string
    {
        return I18n::translate("hksagentur.schema.cardinalDirection.{$this->key()}", $this->name(), $locale);
    }

    public function __toString(): string
    {
        return $this->toString();
    }
}
