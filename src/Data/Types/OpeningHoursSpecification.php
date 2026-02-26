<?php

namespace Hks\Schema\Data\Types;

use Hks\Schema\Data\Collections\TimeRanges;
use Hks\Schema\Data\Collections\WeekDays;
use Hks\Schema\Data\Contracts\Arrayable;
use Hks\Schema\Data\Contracts\Htmlable;
use Hks\Schema\Data\Contracts\Localizable;
use Hks\Schema\Data\Exceptions\ValidationException;
use Kirby\Toolkit\Html;
use Throwable;

readonly class OpeningHoursSpecification extends DataType implements Arrayable, Htmlable, Localizable
{
    public function __construct(
        public WeekDays $weekDays,
        public TimeRanges $timeRanges,
        public array $data = []
    ) {
        if ($weekDays->isEmpty()) {
            throw new ValidationException(
                'Invalid opening hours specification: At least one week day must be specified.'
            );
        }

        if ($timeRanges->isEmpty()) {
            throw new ValidationException(
                'Invalid opening hours specification: At least one time range must be specified.'
            );
        }
    }

    public static function from(array|self $value): self
    {
        return match (true) {
            $value instanceof static => $value,
            default => static::fromArray((array) $value),
        };
    }

    public static function tryFrom(array|self $value): ?static
    {
        try {
            return static::from($value);
        } catch (Throwable) {
            return null;
        }
    }

    public static function fromArray(array $value): static
    {
        return new static(
            weekDays: new WeekDays(array_map(
                WeekDay::fromName(...),
                $value['weekDays'] ?? [],
            )),
            timeRanges: new TimeRanges(array_map(
                TimeRange::from(...),
                $value['timeRanges'] ?? [],
            )),
            data: $value['data'] ?? [],
        );
    }

    public function weekDays(): WeekDays
    {
        return $this->weekDays;
    }

    public function timeRanges(): TimeRanges
    {
        return $this->timeRanges;
    }

    /** @return array<string, mixed> */
    public function data(): array
    {
        return $this->data;
    }

    public function simplify(): static
    {
        throw new \Exception('Not implemented');
    }

    public function format(?string $dayFormat = null, string $daySeparator = ' – ', int|string|null $timeFormat = null, string $timeSeparator = ' – ', ?string $locale = null): string
    {
        return sprintf(
            '%s: %s',
            $this->weekDays->format($dayFormat, $daySeparator, locale: $locale),
            $this->timeRanges->format($timeFormat, $timeSeparator, locale: $locale),
        );
    }

    public function toArray(): array
    {
        return [
            'weekDays' => $this->weekDays->toArray(),
            'timeRanges' => $this->timeRanges->toArray(),
            'data' => $this->data,
        ];
    }

    public function toString(): string
    {
        return sprintf(
            '%s: %s',
            $this->weekDays->toString(),
            $this->timeRanges->toString(),
        );
    }

    public function toLocaleString(?string $locale = null, array $options = []): string
    {
        return $this->format(locale: $locale);
    }

    public function toHtml(array $attributes = []): string
    {
        return Html::tag('time', [
            $this->toLocaleString(),
        ], [
            'datetime' => $this->toString(),
            ...$attributes
        ]);
    }
}
