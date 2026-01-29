<?php

namespace Hks\Schema\Data\Types;

use Hks\Schema\Data\Contracts\Htmlable;
use Kirby\Toolkit\Str;
use Stringable;
use Throwable;

abstract readonly class StringType extends DataType implements Htmlable
{
    public function __construct(
        public string $value
    ) {
    }

    public static function from(string|self|Stringable $value): static
    {
        return match (true) {
            $value instanceof static => $value,
            default => new static((string) $value),
        };
    }

    public static function tryFrom(string|self|Stringable $value): ?static
    {
        try {
            return static::from($value);
        } catch (Throwable) {
            return null;
        }
    }

    public function value(): string
    {
        return $this->value;
    }

    public function toString(): string
    {
        return $this->value;
    }

    public function toHtml(array $attributes = []): string
    {
        return Str::esc($this->value, context: 'html');
    }

    public function toPlainText(): string
    {
        return strip_tags($this->toHtml());
    }
}
