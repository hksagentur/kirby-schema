<?php

namespace Hks\Schema\Data\Types;

use Hks\Schema\Data\Contracts\Arrayable;
use Hks\Schema\Data\Contracts\Htmlable;
use Hks\Schema\Data\Contracts\Localizable;
use InvalidArgumentException;
use Kirby\Toolkit\Html;
use Kirby\Toolkit\I18n;
use Throwable;

readonly class Length extends DataType implements Arrayable, Htmlable, Localizable
{
    public function __construct(
        public float $value,
        public LengthUnit $unit = LengthUnit::Meter,
    ) {
    }

    public static function from(int|float|self $value): static
    {
        return ($value instanceof self) ? $value : new static((float) $value);
    }

    public static function tryFrom(int|float|self $value): ?static
    {
        try {
            return static::from($value);
        } catch (Throwable) {
            return null;
        }
    }

    public static function min(self $value, self ...$others): static
    {
        $smallest = $value;

        foreach ($others as $other) {
            if ($other->isSmallerThan($smallest)) {
                $smallest = $other;
            }
        }

        return $smallest;
    }

    public static function max(self $value, self ...$others): static
    {
        $greatest = $value;

        foreach ($others as $other) {
            if ($other->isGreaterThan($greatest)) {
                $greatest = $other;
            }
        }

        return $greatest;
    }

    public function isGreaterThan(int|float|self $other): bool
    {
        return $this->toMeters()->value() > static::of($other)->toMeters()->value();
    }

    public function isGreaterThanOrEqualTo(int|float|self $other): bool
    {
        return $this->toMeters()->value() >= static::of($other)->toMeters()->value();
    }

    public function isSmallerThan(int|float|self $other): bool
    {
        return $this->toMeters()->value() < static::of($other)->toMeters()->value();
    }

    public function isSmallerThanOrEqualTo(int|float|self $other): bool
    {
        return $this->toMeters()->value() <= static::of($other)->toMeters()->value();
    }

    public function equals(DataType $other): bool
    {
        if (! ($other instanceof self)) {
            return false;
        }

        $delta = abs($this->toMeters()->value() - $this->toMeters()->value());

        if ($delta >= PHP_FLOAT_EPSILON) {
            return false;
        }

        return true;
    }

    public function value(): float
    {
        return $this->value;
    }

    public function unit(): LengthUnit
    {
        return $this->unit;
    }

    public function format(?string $locale = null): string
    {
        return $this->toLocaleString($locale);
    }

    public function normalize(): static
    {
        return $this->convertTo(LengthUnit::forValue(
            $this->toMeters()->value(),
            $this->unit()->system(),
        ));
    }

    public function add(int|float|self $other): static
    {
        $length = new static(
            $this->toMeters()->value() + static::of($other)->toMeters()->value()
        );

        return $length->convertTo($this->unit());
    }

    public function sub(int|float|self $other): static
    {
        $length = new static(
            $this->toMeters()->value() - static::of($other)->toMeters()->value()
        );

        return $length->convertTo($this->unit());
    }

    public function convertTo(LengthUnit $unit): static
    {
        return new static(
            value: $this->value() * $this->unit()->toMeters() / $unit->toMeters(),
            unit: $unit,
        );
    }

    public function toMiles(): static
    {
        return $this->convertTo(LengthUnit::Mile);
    }

    public function toMeters(): static
    {
        return $this->convertTo(LengthUnit::Meter);
    }

    public function toKilometers(): static
    {
        return $this->convertTo(LengthUnit::Kilometer);
    }

    public function toArray(): array
    {
        return [
            'value' => $this->value(),
            'unit' => $this->unit()->symbol(),
        ];
    }

    public function toHtml(array $attributes = []): string
    {
        return Html::tag('data', $this->toLocaleString(), [
            'value' => $this->toMeters()->value(),
            'data-unit' => $this->unit()->symbol(),
            ...$attributes,
        ]);
    }

    public function toString(): string
    {
        return sprintf(
            '%f %s',
            $this->value(),
            $this->unit()->symbol(),
        );
    }

    public function toLocaleString(?string $locale = null): string
    {
        return sprintf(
            '%s %s',
            I18n::formatNumber($this->value(), $locale),
            $this->unit()->symbol(),
        );
    }

    public function jsonSerialize(): float
    {
        return $this->toMeters()->value();
    }
}
