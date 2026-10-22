<?php

namespace Hks\Schema\Data\Types;

use Hks\Schema\Data\Contracts\Localizable;
use Kirby\Toolkit\I18n;

enum AreaUnit: string implements Localizable
{
    public const float SQUARE_METERS_PER_SQUARE_FOOT = 0.09290304;
    public const float SQUARE_FEET_PER_ACRE = 43560;
    public const float ACRES_PER_SQUARE_MILE = 640;

    case SquareMeter = 'm²';
    case Hectare = 'ha';
    case SquareKilometer = 'km²';

    case SquareFoot = 'ft²';
    case Acre = 'ac';
    case SquareMile = 'mi²';

    public static function of(string|self $value): ?static
    {
        if ($value instanceof self) {
            return $value;
        }

        return static::tryFrom($value);
    }

    public static function forValue(float $squareMeters, UnitSystem $system = UnitSystem::Metric): static
    {
        return match ($system) {
            UnitSystem::Imperial => match (true) {
                $squareMeters >= self::SquareMile->toSquareMeters() => self::SquareMile,
                $squareMeters >= self::Acre->toSquareMeters() => self::Acre,
                default => self::SquareFoot,
            },
            UnitSystem::Metric => match (true) {
                $squareMeters >= self::SquareKilometer->toSquareMeters() => self::SquareKilometer,
                $squareMeters >= self::Hectare->toSquareMeters() => self::Hectare,
                default => self::SquareMeter,
            },
        };
    }

    public static function all(?UnitSystem $system = null): array
    {
        $units = [];

        foreach (self::cases() as $unit) {
            if (is_null($system) || $system === $unit->system()) {
                $units[] = $unit;
            }
        }

        return $units;
    }

    public function isMetric(): bool
    {
        return $this->system() === UnitSystem::Metric;
    }

    public function isImperial(): bool
    {
        return $this->system() === UnitSystem::Imperial;
    }

    public function label(?string $locale = null): string
    {
        return $this->toLocaleString($locale);
    }

    public function key(): string
    {
        return lcfirst($this->name);
    }

    public function name(): string
    {
        return $this->name;
    }

    public function symbol(): string
    {
        return $this->value;
    }

    public function system(): UnitSystem
    {
        return match ($this) {
            self::SquareFoot, self::Acre, self::SquareMile => UnitSystem::Imperial,
            default => UnitSystem::Metric,
        };
    }

    public function toSquareMeters(): float
    {
        return match ($this) {
            self::SquareMeter => 1.0,
            self::Hectare => 10_000.0,
            self::SquareKilometer => 1_000_000.0,
            self::SquareFoot => self::SQUARE_METERS_PER_SQUARE_FOOT,
            self::Acre => self::SQUARE_METERS_PER_SQUARE_FOOT * self::SQUARE_FEET_PER_ACRE,
            self::SquareMile => self::SQUARE_METERS_PER_SQUARE_FOOT * self::SQUARE_FEET_PER_ACRE * self::ACRES_PER_SQUARE_MILE,
        };
    }

    public function toString(): string
    {
        return $this->name();
    }

    public function toLocaleString(?string $locale = null, array $options = []): string
    {
        return I18n::translate("hksagentur.schema.unit.{$this->key()}", $this->name(), $locale);
    }
}
