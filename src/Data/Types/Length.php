<?php

namespace Hks\Schema\Data\Types;

use Hks\Schema\Data\Concerns\ComparesNumbers;
use Hks\Schema\Data\Contracts\Arrayable;
use Hks\Schema\Data\Contracts\Comparable;
use Hks\Schema\Data\Contracts\Htmlable;
use Hks\Schema\Data\Contracts\Localizable;
use Hks\Schema\Data\Exceptions\TypeMismatchException;
use Kirby\Toolkit\I18n;

readonly class Length extends NumberType implements Arrayable, Comparable, Htmlable, Localizable
{
    use ComparesNumbers;

    public LengthUnit $unit;

    public function __construct(float $value, LengthUnit $unit = LengthUnit::Meter)
    {
        parent::__construct($value);

        $this->unit = $unit;
    }

    public function isSmallerThan(int|float|self $other): bool
    {
        return $this->isLessThan($other);
    }

    public function isSmallerThanOrEqualTo(int|float|self $other): bool
    {
        return $this->isLessThanOrEqualTo($other);
    }

    public function isLargerThan(int|float|self $other): bool
    {
        return $this->isGreaterThan($other);
    }

    public function isLargerThanOrEqualTo(int|float|self $other): bool
    {
        return $this->isGreaterThanOrEqualTo($other);
    }

    public function unit(): LengthUnit
    {
        return $this->unit;
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
            $this->toMeters()->value() + static::from($other)->toMeters()->value()
        );

        return $length->convertTo($this->unit());
    }

    public function subtract(int|float|self $other): static
    {
        $length = new static(
            $this->toMeters()->value() - static::from($other)->toMeters()->value()
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

    public function format(?string $locale = null): string
    {
        return $this->toLocaleString($locale);
    }

    public function equals(DataType $other): bool
    {
        if (! ($other instanceof self)) {
            return false;
        }

        $delta = abs($this->toMeters()->value() - $other->toMeters()->value());

        if ($delta >= PHP_FLOAT_EPSILON) {
            return false;
        }

        return true;
    }

    public function compare(mixed $other): int
    {
        $length = static::tryFrom($other);

        if (! $length) {
            throw TypeMismatchException::between($this, $other);
        }

        return $this->toMeters()->value() <=> $length->toMeters()->value();
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

    public function toString(): string
    {
        return sprintf(
            '%f %s',
            $this->value(),
            $this->unit()->symbol(),
        );
    }

    public function toLocaleString(?string $locale = null, array $options = []): string
    {
        return sprintf(
            '%s %s',
            I18n::formatNumber($this->value(), $locale),
            $this->unit()->symbol(),
        );
    }

    public function toArray(): array
    {
        return [
            'value' => $this->value(),
            'unit' => $this->unit()->symbol(),
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
