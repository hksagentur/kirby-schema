<?php

namespace Hks\Schema\Data\Types;

use Kirby\Toolkit\Str;

enum LinkTarget: string
{
    case Self = '_self';
    case Blank = '_blank';
    case Parent = '_parent';
    case Top = '_top';

    public static function of(string|self|null $value): ?static
    {
        if ($value instanceof self) {
            return $value;
        }

        return static::tryFrom($value ?? '');
    }

    public static function default(): static
    {
        return self::Self;
    }

    public function isDefault(): bool
    {
        return $this === self::default();
    }

    public function key(): string
    {
        return Str::lower($this->name);
    }

    public function name(): string
    {
        return $this->name;
    }

    public function value(): string
    {
        return $this->value;
    }

    public function toString(): string
    {
        return $this->value;
    }
}
