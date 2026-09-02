<?php

namespace Hks\Schema\Data\Types;

use DateTimeImmutable;
use DateTimeInterface;
use Hks\Schema\Data\Contracts\Arrayable;
use Hks\Schema\Data\Contracts\Htmlable;
use Hks\Schema\Data\Contracts\Schemable;
use Hks\Schema\Data\Exceptions\ValidationException;
use Kirby\Toolkit\A;
use Throwable;

readonly class OpeningHoursException extends DataType implements Arrayable, Htmlable, Schemable
{
    public function __construct(
        public DateTimeImmutable $date,
        public TimeRange $timeRange,
        public array $data = [],
    ) {
    }

    public static function from(array|self $value): self
    {
        return match (true) {
            $value instanceof static => $value,
            default => static::fromArray($value),
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
        if (! isset($value['date'], $value['timeRange'])) {
            throw new ValidationException(
                'The given array does not represent a valid opening hours exception: a date and time range must be specified.'
            );
        }

        return new static(
            date: new DateTimeImmutable($value['date']),
            timeRange: TimeRange::from($value['timeRange']),
            data: $value['data'] ?? [],
        );
    }

    public function date(): DateTimeImmutable
    {
        return $this->date;
    }

    public function timeRange(): TimeRange
    {
        return $this->timeRange;
    }

    public function data(?string $key = null): mixed
    {
        return A::get($this->data, $key);
    }

    public function contains(DateTimeInterface $date): bool
    {
        return $this->date->format('Y-m-d') === $date->format('Y-m-d');
    }

    public function toString(): string
    {
        return $this->timeRange->toString();
    }

    public function toHtml(array $attributes = []): string
    {
        return $this->timeRange->toHtml($attributes);
    }

    public function toArray(): array
    {
        return [
            'timeRange' => $this->timeRange->toArray(),
            'date' => $this->date->format('Y-m-d'),
            'data' => $this->data,
        ];
    }

    public function toStructuredData(): array
    {
        return [
            '@type' => 'OpeningHoursSpecification',
            'opens' => $this->timeRange->start()->toString(),
            'closes' => $this->timeRange->end()->toString(),
            'validFrom' => $this->date->format('Y-m-d'),
            'validThrough' => $this->date->format('Y-m-d'),
        ];
    }
}
