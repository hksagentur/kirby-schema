<?php

namespace Hks\Schema\Data\Types;

use Hks\Schema\Data\Contracts\Arrayable;
use Hks\Schema\Data\Contracts\Htmlable;
use Throwable;

readonly class OpeningHoursException extends DataType implements Arrayable, Htmlable
{
    public function __construct(
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
        return new static(
            timeRange: TimeRange::from($value['timeRange']),
            data: $value['data'] ?? [],
        );
    }

    public function timeRange(): TimeRange
    {
        return $this->timeRange;
    }

    /** @return array<string, mixed> */
    public function data(): array
    {
        return $this->data;
    }

    public function toArray(): array
    {
        throw new \Exception('Not implemented');
    }

    public function toString(): string
    {
        throw new \Exception('Not implemented');
    }

    public function toHtml(array $attributes = []): string
    {
        throw new \Exception('Not implemented');
    }
}
