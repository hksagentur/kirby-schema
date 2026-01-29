<?php

namespace Hks\Schema\Data\Concerns;

use Kirby\Toolkit\Collection;
use Kirby\Toolkit\Str;

trait TransformsStrings
{
    public function isEmpty(): bool
    {
        return $this->length() === 0;
    }

    public function isNotEmpty(): bool
    {
        return $this->length() !== 0;
    }

    public function length(): int
    {
        return Str::length($this->value);
    }

    public function escape(string $context = 'html'): static
    {
        return new static(Str::esc((string) $this, $context));
    }

    public function excerpt(int $limit = 140, string $end = '…'): static
    {
        return new static(Str::excerpt((string) $this, chars: $limit, strip: true, rep: $end));
    }

    public function limit(int $length = 140, string $end = '…'): static
    {
        return new static(Str::short((string) $this, $length, $end));
    }

    public function lower(): static
    {
        return new static(Str::lower((string) $this));
    }

    public function upper(): static
    {
        return new static(Str::upper((string) $this));
    }

    public function ucfirst(): static
    {
        return new static(Str::ucfirst((string) $this));
    }

    public function ucwords(): static
    {
        return new static(Str::ucwords((string) $this));
    }

    public function kebab(): static
    {
        return new static(Str::kebab((string) $this));
    }

    public function studly(): static
    {
        return new static(Str::studly((string) $this));
    }

    public function snake(string $delimiter = '_'): static
    {
        return new static(Str::snake((string) $this, $delimiter));
    }

    public function slug(?string $separator = null, int $maxlength = 128): static
    {
        return new static(Str::slug((string) $this, separator: $separator, maxlength: $maxlength));
    }

    public function substr(int $start = 0, ?int $length = null): static
    {
        return new static(Str::substr((string) $this, $start, $length));
    }

    public function trim(string $characters = ' '): static
    {
        return new static(Str::trim((string) $this, $characters));
    }

    public function ltrim(string $characters = ' '): static
    {
        return new static(Str::ltrim((string) $this, $characters));
    }

    public function rtrim(string $characters = ' '): static
    {
        return new static(Str::rtrim((string) $this, $characters));
    }

    public function replace(string|array|Collection $search, string|array|Collection $replace, int|array $limit = -1): static
    {
        return new static(Str::replace((string) $this, $search, $replace, $limit));
    }

    public function template(array $data, array $options = []): static
    {
        return new static(Str::template((string) $this, $data, $options));
    }

    public function wrap(string $before, ?string $after = null): static
    {
        return new static(Str::wrap((string) $this, $before, $after));
    }
}
