<?php

namespace Hks\Schema\Data\Types;

use DateTimeInterface;
use Hks\Schema\Data\Attributes\ArrayInput;
use Hks\Schema\Data\Contracts\Arrayable;
use Hks\Schema\Data\Contracts\Comparable;
use Hks\Schema\Data\Contracts\Htmlable;
use Hks\Schema\Data\Contracts\Localizable;
use Hks\Schema\Data\Exceptions\TypeMismatchException;
use Hks\Schema\Data\Exceptions\ValidationException;
use Kirby\Toolkit\Str;
use Throwable;

#[ArrayInput]
readonly class TimeRange extends DataType implements Arrayable, Comparable, Htmlable, Localizable
{
    public const string SEPARATOR = '/';

    public function __construct(
        public Time $start,
        public Time $end,
    ) {
    }

    public static function from(string|array|self $value): self
    {
        return match (true) {
            $value instanceof static => $value,
            is_array($value) => static::fromArray($value),
            default => static::fromIsoString((string) $value),
        };
    }

    public static function tryFrom(string|array|self $value): ?static
    {
        try {
            return static::from($value);
        } catch (Throwable) {
            return null;
        }
    }

    public static function fromArray(array $value): self
    {
        [$start, $end] = match (true) {
            isset($value['start'], $value['end']) => [
                $value['start'],
                $value['end'],
            ],
            isset($value[0], $value[1]) => [
                $value[0],
                $value[1],
            ],
            default => throw new ValidationException(
                'The given array does not represent a valid time range.'
            ),
        };

        return new static(
            start: Time::from($start),
            end: Time::from($end),
        );
    }

    public static function fromIsoString(string $value): self
    {
        return new static(
            start: Time::from(Str::before($value, '/')),
            end: Time::from(Str::after($value, '/')),
        );
    }

    public function isOverNight(): bool
    {
        return $this->start->isAfter($this->end);
    }

    public function start(): Time
    {
        return $this->start;
    }

    public function end(): Time
    {
        return $this->end;
    }

    public function contains(string|Time|DateTimeInterface $time): bool
    {
        $time = Time::from($time);

        if ($this->isOverNight()) {
            return $time->isAfterOrEqualTo($this->start) || $time->isBeforeOrEqualTo($this->end);
        }

        return $time->isBetween($this->start, $this->end);
    }

    public function touches(self $other): bool
    {
        return $this->start->isEqualTo($other->end) || $this->end->isEqualTo($other->start);
    }

    public function overlaps(self $other): bool
    {
        return $this->contains($other->start) || $this->contains($other->end);
    }

    public function merge(self $other): self
    {
        return new static(
            start: $this->start->min($other->start),
            end: $this->end->max($other->end),
        );
    }

    public function compare(mixed $other): int
    {
        if (! ($other instanceof self)) {
            throw TypeMismatchException::between($this, $other);
        }

        return $this->start->compare($other->start);
    }

    public function format(int|string|null $timeFormat = null, string $timeSeparator = ' – ', ?string $locale = null): string
    {
        return sprintf(
            '%s%s%s',
            $this->start->format($timeFormat, $locale),
            $timeSeparator,
            $this->end->format($timeFormat, $locale),
        );
    }

    /** @return string[] */
    public function toArray(): array
    {
        return [
            $this->start->toString(),
            $this->end->toString(),
        ];
    }

    public function toString(): string
    {
        return $this->toIsoString();
    }

    public function toIsoString(): string
    {
        return sprintf(
            '%s%s%s',
            $this->start->toIsoString(),
            self::SEPARATOR,
            $this->end->toIsoString(),
        );
    }

    public function toLocaleString(?string $locale = null, array $options = []): string
    {
        return $this->format(locale: $locale);
    }

    public function jsonSerialize(): string
    {
        return $this->toIsoString();
    }
}
