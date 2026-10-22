<?php

namespace Hks\Schema\Data\Types;

use Hks\Schema\Data\Concerns\ComparesNumbers;
use Hks\Schema\Data\Contracts\Arrayable;
use Hks\Schema\Data\Contracts\Comparable;
use Hks\Schema\Data\Contracts\Htmlable;
use Hks\Schema\Data\Contracts\Localizable;
use Hks\Schema\Data\Exceptions\TypeMismatchException;
use Hks\Schema\Toolkit\Number;
use Kirby\Toolkit\A;

readonly class Area extends NumberType implements Arrayable, Comparable, Htmlable, Localizable
{
    use ComparesNumbers;

    public AreaUnit $unit;

    public function __construct(float $value, AreaUnit $unit = AreaUnit::SquareMeter)
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

    public function unit(): AreaUnit
    {
        return $this->unit;
    }

    public function normalize(): static
    {
        return $this->convertTo(AreaUnit::forValue(
            $this->toSquareMeters()->value(),
            $this->unit()->system(),
        ));
    }

    public function add(int|float|self $other): static
    {
        $length = new static(
            $this->toSquareMeters()->value() + static::from($other)->toSquareMeters()->value()
        );

        return $length->convertTo($this->unit());
    }

    public function subtract(int|float|self $other): static
    {
        $length = new static(
            $this->toSquareMeters()->value() - static::from($other)->toSquareMeters()->value()
        );

        return $length->convertTo($this->unit());
    }

    public function convertTo(AreaUnit $unit): static
    {
        return new static(
            value: $this->value() * $this->unit()->toSquareMeters() / $unit->toSquareMeters(),
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

        $delta = abs($this->toSquareMeters()->value() - $other->toSquareMeters()->value());

        if ($delta >= PHP_FLOAT_EPSILON) {
            return false;
        }

        return true;
    }

    public function compare(mixed $other): int
    {
        $area = static::tryFrom($other);

        if (! $area) {
            throw TypeMismatchException::between($this, $other);
        }

        return $this->toSquareMeters()->value() <=> $area->toSquareMeters()->value();
    }

    public function toSquareMeters(): static
    {
        return $this->convertTo(AreaUnit::SquareMeter);
    }

    public function toHectares(): static
    {
        return $this->convertTo(AreaUnit::Hectare);
    }

    public function toSquareFeet(): static
    {
        return $this->convertTo(AreaUnit::SquareFoot);
    }

    public function toString(): string
    {
        return sprintf(
            '%f %s',
            $this->value(),
            $this->unit()->symbol(),
        );
    }

    /**
     * @param array{separator?: string, precision?: int, maxPrecision?: int, roundingMode?: string, roundingIncrement?: int|float} $options
     */
    public function toLocaleString(?string $locale = null, array $options = []): string
    {
        $separator = $options['separator'] ?? "\u{00A0}";
        $options = A::without($options, 'separator');

        return Number::format($this->value(), $locale, $options) . $separator . $this->unit()->symbol();
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
