<?php

namespace Hks\Schema\Data;

use Hks\Schema\Formatter\FractionHtmlFormatter;
use Hks\Schema\Formatter\FractionTextFormatter;
use Hks\Schema\Toolkit\Math;
use InvalidArgumentException;
use Kirby\Cms\Html;
use Kirby\Toolkit\Str;
use RuntimeException;

readonly class Fraction extends CompositeDataValue
{
    public int $numerator;
    public int $denominator;

    public function __construct(int $numerator, int $denominator)
    {
        if ($denominator === 0) {
            throw new InvalidArgumentException(
                'The denominator of a fraction cannot be zero.'
            );
        }

        if ($denominator < 0) {
            $numerator = -1 * $numerator;
            $denominator = -1 * $denominator;
        }

        $this->numerator = $numerator;
        $this->denominator = $denominator;
    }

    public static function of(int|string|self $value): static
    {
        if ($value instanceof static) {
            return $value;
        }

        if (is_int($value)) {
            return static::fromInt($value);
        }

        if (is_string($value) && str_contains($value, '/')) {
            return static::fromString($value);
        }

        throw new InvalidArgumentException(sprintf(
            'The given value "%s" does not represent a valid fraction.',
            $value,
        ));
    }

    public static function from(int $numerator, int $denominator): static
    {
        return new static($numerator, $denominator);
    }

    public static function tryFrom(int $numerator, int $denominator): ?static
    {
        try {
            return static::from($numerator, $denominator);
        } catch (InvalidArgumentException) {
            return null;
        }
    }

    public static function fromArray(array $attributes): static
    {
        return new static(...$attributes);
    }

    public static function fromInt(int $value): static
    {
        return new static($value, 1);
    }

    public static function fromString(string $value): static
    {
        $parts = Str::split($value, '/');

        if (count($parts) !== 2) {
            throw new InvalidArgumentException(sprintf(
                'The given value "%s" does not represent a valid fraction.',
                $value,
            ));
        }

        return new static(
            numerator: (int) $parts[0],
            denominator: (int) $parts[1],
        );
    }

    public function numerator(): int
    {
        return $this->numerator;
    }

    public function denominator(): int
    {
        return $this->denominator;
    }

    public function quotient(): float
    {
        return $this->numerator / $this->denominator;
    }

    public function add(int|string|self $other): static
    {
        $other = static::of($other);

        $fraction = new static(
            numerator: ($this->numerator * $other->denominator) + ($other->numerator * $this->denominator),
            denominator: $this->denominator * $other->denominator,
        );

        return $fraction->simplify();
    }

    public function subtract(int|string|self $other): static
    {
        $other = static::of($other);

        $fraction = new static(
            numerator: ($this->numerator * $other->denominator) - ($other->numerator * $this->denominator),
            denominator: $this->denominator * $other->denominator,
        );

        return $fraction->simplify();
    }

    public function multiplyBy(int|string|self $other): static
    {
        $other = static::of($other);

        $fraction = new static(
            numerator: $this->numerator * $other->numerator,
            denominator: $this->denominator * $other->denominator,
        );

        return $fraction->simplify();
    }

    public function divideBy(int|string|self $other): static
    {
        $other = static::of($other);

        $fraction = new static(
            numerator: $this->numerator * $other->denominator,
            denominator: $this->denominator * $other->numerator,
        );

        return $fraction->simplify();
    }

    public function reciprocal(): static
    {
        if ($this->numerator === 0) {
            throw new RuntimeException(
                'Cannot calculate the reciprocal of zero'
            );
        }

        return new static(
            numerator: $this->denominator,
            denominator: $this->numerator,
        );
    }

    public function simplify(): static
    {
        $divisor = $this->greatestCommonDivisor();

        if ($divisor === 1) {
            return $this;
        }

        return new static(
            numerator: $this->numerator / $divisor,
            denominator: $this->denominator / $divisor,
        );
    }

    public function isWhole(): bool
    {
        return $this->simplify()->denominator() === 1;
    }

    public function isCanonical(): bool
    {
        return $this->greatestCommonDivisor() === 1;
    }

    public function isEqualTo(int|string|self $other): bool
    {
        return $this->compareTo($other) === 0;
    }

    public function isLessThan(int|string|self $other): bool
    {
        return $this->compareTo($other) < 0;
    }

    public function isLessThanOrEqualTo(int|string|self $other): bool
    {
        return $this->compareTo($other) <= 0;
    }

    public function isGreaterThan(int|string|self $other): bool
    {
        return $this->compareTo($other) > 0;
    }

    public function isGreaterThanOrEqualTo(int|string|self $other): bool
    {
        return $this->compareTo($other) >= 0;
    }

    public function compareTo(int|string|self $other): int
    {
        return $this->subtract($other)->numerator() <=> 0;
    }

    public function toArray(): array
    {
        return [$this->numerator, $this->denominator];
    }

    public function toFloat(): float
    {
        return $this->numerator / $this->denominator;
    }

    public function toInt(): int
    {
        [$numerator, $denominator] = $this->simplify()->toArray();

        if ($denominator !== 1) {
            throw new RuntimeException(
                'This fraction can not be represented as integer value.'
            );
        }

        return $numerator;
    }

    public function toString(): string
    {
        return $this->format(FractionTextFormatter::class);
    }

    public function toHtml(array $attributes = []): string
    {
        return $this->format(FractionHtmlFormatter::class, ['attributes' => $attributes]);
    }

    protected function greatestCommonDivisor(): int
    {
        return Math::greatestCommonDivisor($this->numerator, $this->denominator);
    }

    protected function leastCommonMultiple(): int
    {
        return Math::leastCommonMultiple($this->numerator, $this->denominator);
    }
}
