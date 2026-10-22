<?php

namespace Hks\Schema\Data\Types;

use Hks\Schema\Data\Attributes\ArrayInput;
use Hks\Schema\Data\Concerns\ComparesNumbers;
use Hks\Schema\Data\Contracts\Arrayable;
use Hks\Schema\Data\Contracts\Comparable;
use Hks\Schema\Data\Contracts\Htmlable;
use Hks\Schema\Data\Contracts\Localizable;
use Hks\Schema\Data\Contracts\Schemable;
use Hks\Schema\Data\Exceptions\TypeMismatchException;
use InvalidArgumentException;
use Kirby\Cms\App;
use Kirby\Toolkit\I18n;
use NumberFormatter;
use Throwable;

#[ArrayInput]
readonly class Money extends DataType implements Arrayable, Comparable, Htmlable, Localizable, Schemable
{
    use ComparesNumbers;

    public int $amount;
    public Currency $currency;

    public function __construct(int $amount, ?Currency $currency = null)
    {
        $this->amount = $amount;
        $this->currency = $currency ?? Currency::default();
    }

    public static function from(int|float|array|self $value): static
    {
        if ($value instanceof self) {
            return $value;
        }

        if (is_array($value)) {
            return static::fromMajorUnits(
                value: (float) ($value['amount'] ?? 0),
                currency: Currency::tryFrom((string) ($value['currency'] ?? '')) ?? Currency::default(),
            );
        }

        return static::fromMajorUnits($value);
    }

    public static function tryFrom(int|float|array|self $value): ?static
    {
        try {
            return static::from($value);
        } catch (Throwable) {
            return null;
        }
    }

    public static function fromMinorUnits(int $value, ?Currency $currency = null): static
    {
        return new static($value, $currency);
    }

    public static function fromMajorUnits(int|float $value, ?Currency $currency = null): static
    {
        $currency ??= Currency::default();

        return new static(
            (int) round($value * (10 ** $currency->minorUnits())),
            $currency,
        );
    }

    public static function assertSameCurrency(self $a, self $b): void
    {
        if (! $a->isSameCurrency($b)) {
            throw new InvalidArgumentException(sprintf(
                'Cannot combine monetary amounts in different currencies ("%s" and "%s").',
                $a->currency->value,
                $b->currency->value,
            ));
        }
    }

    public function isSameCurrency(self $other): bool
    {
        return $this->currency->equals($other->currency);
    }

    /**
     * Get the amount in minor units, e.g. cents.
     */
    public function value(): int
    {
        return $this->amount;
    }

    public function amount(): int
    {
        return $this->amount;
    }

    public function currency(): Currency
    {
        return $this->currency;
    }

    public function amountOf(int|float|self $other): int
    {
        $money = ($other instanceof self)
            ? $other
            : static::fromMajorUnits($other, $this->currency);

        static::assertSameCurrency($this, $money);

        return $money->amount;
    }

    public function add(int|float|self $other): static
    {
        return new static($this->amount + $this->amountOf($other), $this->currency);
    }

    public function subtract(int|float|self $other): static
    {
        return new static($this->amount - $this->amountOf($other), $this->currency);
    }

    public function multiplyBy(int|float $factor): static
    {
        return new static((int) round($this->amount * $factor), $this->currency);
    }

    public function divideBy(int|float $divisor): static
    {
        return new static((int) round($this->amount / $divisor), $this->currency);
    }

    public function equals(DataType $other): bool
    {
        if (! ($other instanceof self)) {
            return false;
        }

        return $this->isSameCurrency($other) && $this->amount === $other->amount;
    }

    public function compare(mixed $other): int
    {
        if (! is_int($other) && ! is_float($other) && ! ($other instanceof self)) {
            throw TypeMismatchException::between($this, $other);
        }

        return $this->amount <=> $this->amountOf($other);
    }

    public function toInt(): int
    {
        return $this->amount;
    }

    public function toFloat(): float
    {
        return $this->toMajorUnits();
    }

    public function toMajorUnits(): float
    {
        return $this->amount / (10 ** $this->currency->minorUnits());
    }

    public function toString(): string
    {
        return sprintf('%.' . $this->currency->minorUnits() . 'f %s', $this->toMajorUnits(), $this->currency->value);
    }

    public function toLocaleString(?string $locale = null, array $options = []): string
    {
        $locale ??= I18n::locale();

        $formatter = new NumberFormatter($locale, NumberFormatter::CURRENCY);
        $formatted = $formatter->formatCurrency($this->toMajorUnits(), $this->currency->value);

        return $formatted !== false ? $formatted : $this->toString();
    }

    public function toHtml(array $attributes = []): string
    {
        return App::instance()->snippet('schema/money', [
            'item' => $this,
            'attrs' => $attributes,
        ]);
    }

    public function toArray(): array
    {
        return [
            'amount' => $this->toMajorUnits(),
            'currency' => $this->currency->value,
        ];
    }

    public function toStructuredData(): array
    {
        return [
            '@type' => 'MonetaryAmount',
            'currency' => $this->currency->value,
            'value' => $this->toMajorUnits(),
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
