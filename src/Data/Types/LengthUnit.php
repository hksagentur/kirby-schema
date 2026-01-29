<?php

namespace Hks\Schema\Data\Types;

use Hks\Schema\Data\Contracts\Localizable;
use Stringable;

enum LengthUnit: string implements Localizable, Stringable
{
    public const float METERS_PER_INCH = 0.0254;
    public const float INCHES_PER_FOOT = 12;
    public const float FEET_PER_MILE = 5280;

    case Millimeter = 'mm';
    case Meter = 'm';
    case Kilometer = 'km';

    case Inch = 'in';
    case Foot = 'ft';
    case Mile = 'mi';

    public static function forValue(float $meters, UnitSystem $system = UnitSystem::Metric): static
    {
        return match ($system) {
            UnitSystem::Imperial => match (true) {
                $meters >= self::Mile->toMeters() => self::Mile,
                $meters >= self::Foot->toMeters() => self::Foot,
                default => self::Inch,
            },
            UnitSystem::Metric => match (true) {
                $meters >= self::Kilometer->toMeters() => self::Kilometer,
                $meters >= self::Meter->toMeters() => self::Meter,
                default => self::Millimeter,
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
        return Str::lower($this->name);
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
            self::Inch, self::Foot, self::Mile => UnitSystem::Imperial,
            default => UnitSystem::Metric,
        };
    }

    public function toMeters(): float
    {
        return match ($this) {
            self::Millimeter => 0.001,
            self::Meter => 1.0,
            self::Kilometer => 1000.0,
            self::Inch => self::METERS_PER_INCH,
            self::Foot => self::METERS_PER_INCH * self::INCHES_PER_FOOT,
            self::Mile => self::METERS_PER_INCH * self::INCHES_PER_FOOT * self::FEET_PER_MILE,
        };
    }

    public function toString(): string
    {
        return $this->name();
    }

    public function toLocaleString(?string $locale = null): string
    {
        return I18n::translate("hksagentur.schema.unit.{$this->key()}", $this->name(), $locale);
    }

    public function __toString(): string
    {
        return $this->toString();
    }
}
