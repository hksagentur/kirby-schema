<?php

namespace Hks\Schema\Data\Types;

use Throwable;

abstract readonly class NumberType extends DataType
{
    public function __construct(
        public float $value
    ) {
    }

    public static function from(int|float|self $value): static
    {
        return match (true) {
            $value instanceof static => $value,
            default => new static($value),
        };
    }

    public static function tryFrom(int|float|self $value): ?static
    {
        try {
            return static::from($value);
        } catch (Throwable) {
            return null;
        }
    }

    public function value(): float
    {
        return $this->value;
    }

    public function toString(): string
    {
        return (string) $this->value;
    }

    public function toInt(): int
    {
        return (int) $this->value;
    }

    public function toFloat(): float
    {
        return $this->value;
    }

    public function jsonSerialize(): float
    {
        return $this->value;
    }
}
