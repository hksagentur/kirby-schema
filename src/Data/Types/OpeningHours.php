<?php

namespace Hks\Schema\Data\Types;

use DateTime;
use DateTimeInterface;
use Hks\Schema\Data\Contracts\Arrayable;
use Hks\Schema\Data\Contracts\Htmlable;
use Hks\Schema\Data\Contracts\Localizable;
use Hks\Schema\Data\Contracts\Schemable;
use Kirby\Cms\App;
use Throwable;

readonly class OpeningHours extends DataType implements Arrayable, Htmlable, Localizable, Schemable
{
    public function __construct(
        /** @var OpeningHoursSpecification[] */
        public array $specifications,
        /** @var OpeningHoursException[] */
        public array $exceptions = [],
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
            specifications: array_map(
                OpeningHoursSpecification::from(...),
                $value['specifications'] ?? [],
            ),
            exceptions: array_map(
                OpeningHoursException::from(...),
                $value['exceptions'] ?? [],
            ),
        );
    }

    public function isOpen(): bool
    {
        throw new \Exception('Not implemented');
    }

    public function isOpenAt(DateTimeInterface $dateTime): bool
    {
        throw new \Exception('Not implemented');
    }

    public function isClosed(): bool
    {
        return ! $this->isOpen();
    }

    public function isClosedAt(DateTimeInterface $dateTime): bool
    {
        return ! $this->isOpenAt($dateTime);
    }

    public function for(WeekDay|DateTimeInterface $weekDay): ?OpeningHoursSpecification
    {
        throw new \Exception('Not implemented');
    }

    public function forToday(): ?OpeningHoursSpecification
    {
        return $this->for(new DateTime('today'));
    }

    public function forTomorrow(): ?OpeningHoursSpecification
    {
        return $this->for(new DateTime('tomorrow'));
    }

    /** @return OpeningHoursSpecification[] */
    public function specifications(): array
    {
        return $this->specifications;
    }

    /** @return OpeningHoursException[] */
    public function exceptions(): array
    {
        return $this->exceptions;
    }

    public function simplify(): static
    {
        throw new \Exception('Not implemented');
    }

    public function toArray(): array
    {
        throw new \Exception('Not implemented');
    }

    public function toSchema(): array
    {
        throw new \Exception('Not implemented');
    }

    public function toString(): string
    {
        throw new \Exception('Not implemented');
    }

    public function toLocaleString(?string $locale = null, array $options = []): string
    {
        throw new \Exception('Not implemented');
    }

    public function toHtml(array $attributes = []): string
    {
        return App::instance()->snippet('schema/opening-hours', [
            'item' => $this,
            'attrs' => $attributes,
        ]);
    }
}
