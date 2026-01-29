<?php

namespace Hks\Schema\Data\Types;

use Hks\Schema\Data\Contracts\Arrayable;
use Hks\Schema\Toolkit\Math;
use InvalidArgumentException;
use Kirby\Toolkit\Html;
use Kirby\Toolkit\Str;
use RuntimeException;
use Stringable;
use Throwable;

readonly class Fraction extends DataType implements Arrayable
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

    public static function from(int|float|string|self|Stringable $value): static
    {
        if ($value instanceof static) {
            return $value;
        }

        if (is_int($value) || (is_float($value) && $value === (int) $value)) {
            return new static((int) $value, 1);
        }

        if (is_float($value) || (is_string($value) && ! Str::contains($value, '/'))) {
            $string = Str::float($value);
            $value = (float) $string;

            $precision = Str::contains($string, '.')
                ? Str::length(Str::after($string, '.'))
                : 0;

            $denominator = (int) pow(10, $precision);
            $numerator = (int) round($value * $denominator);

            return (new static($numerator, $denominator))->simplify();
        }

        $value = (string) $value;

        if (! Str::contains($value, '/')) {
            throw new InvalidArgumentException(sprintf(
                'The given value "%s" does not represent a valid fraction.',
                $value,
            ));
        }

        [$numerator, $denominator] = Str::split($value, '/');

        return new static(
            (int) $numerator,
            (int) $denominator
        );
    }

    public static function tryFrom(int|float|string|self|Stringable $value): ?static
    {
        try {
            return static::from($value);
        } catch (Throwable) {
            return null;
        }
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
        $other = static::from($other);

        $fraction = new static(
            numerator: ($this->numerator * $other->denominator) + ($other->numerator * $this->denominator),
            denominator: $this->denominator * $other->denominator,
        );

        return $fraction->simplify();
    }

    public function subtract(int|string|self $other): static
    {
        $other = static::from($other);

        $fraction = new static(
            numerator: ($this->numerator * $other->denominator) - ($other->numerator * $this->denominator),
            denominator: $this->denominator * $other->denominator,
        );

        return $fraction->simplify();
    }

    public function multiplyBy(int|string|self $other): static
    {
        $other = static::from($other);

        $fraction = new static(
            numerator: $this->numerator * $other->numerator,
            denominator: $this->denominator * $other->denominator,
        );

        return $fraction->simplify();
    }

    public function divideBy(int|string|self $other): static
    {
        $other = static::from($other);

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
        return [
            'numerator' => $this->numerator,
            'denominator' => $this->denominator,
        ];
    }

    public function toFloat(): float
    {
        return $this->numerator / $this->denominator;
    }

    public function toInt(): int
    {
        $fraction = $this->simplify();

        if (! $fraction->isWhole()) {
            throw new RuntimeException(
                'This fraction can not be represented as integer value.'
            );
        }

        return $fraction->numerator();
    }

    public function toString(): string
    {
        return ($this->denominator === 1) ? "{$this->numerator}" : "{$this->numerator}&frasl;{$this->denominator}";
    }

    public function toHtml(array $attributes = []): string
    {
        return Html::tag('math', [
            Html::tag('mfrac', [
                Html::tag('mn', (string) $this->numerator),
                Html::tag('mn', (string) $this->denominator),
            ]),
        ], $attributes);
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
