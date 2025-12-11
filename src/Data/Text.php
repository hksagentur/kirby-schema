<?php

namespace Hks\Schema\Data;

use InvalidArgumentException;
use Kirby\Toolkit\Str;
use Stringable;

readonly class Text extends DataValue
{
    public function __construct(
        public string|Stringable $value
    ) {
        $this->validate();
    }

    public static function from(string|Stringable $value): static
    {
        return new static($value);
    }

    public static function tryFrom(string|null|Stringable $value): ?static
    {
        try {
            return static::from($value);
        } catch (InvalidArgumentException) {
            return null;
        }
    }

    public function isEmpty(): bool
    {
        return $this->length() === 0;
    }

    public function value(): string
    {
        return (string) $this->value;
    }

    public function length(): int
    {
        return Str::length($this->value());
    }

    public function excerpt(int $length = 140, string $omission = '…'): static
    {
        return Text::from(Str::excerpt($this->value(), $length, rep: $omission));
    }

    public function toString(): string
    {
        return $this->value();
    }

    protected function validate(): void
    {
        // Empty
    }
}
