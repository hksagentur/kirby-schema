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
        $specifications = [];

        foreach (WeekDay::cases() as $weekDay) {
            $items = $value[$weekDay->key()] ?? [];

            if ($items === []) {
                continue;
            }

            $specifications[] = OpeningHoursSpecification::fromArray([
                'weekDays' => [$weekDay->name()],
                'timeRanges' => array_column($items, 'timespan'),
            ]);
        }

        $exceptions = array_map(
            OpeningHoursException::from(...),
            $value['exceptions'] ?? [],
        );

        return (new static($specifications, $exceptions))->simplify();
    }

    public function isOpen(): bool
    {
        return $this->isOpenAt(new DateTime());
    }

    public function isOpenAt(DateTimeInterface $dateTime): bool
    {
        $exception = $this->exceptionFor($dateTime);

        if ($exception !== null) {
            return $exception->timeRange()->contains($dateTime);
        }

        $specification = $this->for($dateTime);

        if ($specification !== null) {
            return $specification->timeRanges()->contains($dateTime);
        }

        return false;
    }

    public function isClosed(): bool
    {
        return ! $this->isOpen();
    }

    public function isClosedAt(DateTimeInterface $dateTime): bool
    {
        return ! $this->isOpenAt($dateTime);
    }

    public function hasException(): bool
    {
        return $this->hasExceptionAt(new DateTime());
    }

    public function hasExceptionAt(DateTimeInterface $dateTime): bool
    {
        return $this->exceptionFor($dateTime) !== null;
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

    public function for(WeekDay|DateTimeInterface $weekDay): ?OpeningHoursSpecification
    {
        $weekDay = $weekDay instanceof DateTimeInterface ? WeekDay::of($weekDay) : $weekDay;

        foreach ($this->specifications as $specification) {
            if ($specification->contains($weekDay)) {
                return $specification;
            }
        }

        return null;
    }

    public function forToday(): ?OpeningHoursSpecification
    {
        return $this->for(new DateTime('today'));
    }

    public function forTomorrow(): ?OpeningHoursSpecification
    {
        return $this->for(new DateTime('tomorrow'));
    }

    public function exceptionFor(DateTimeInterface $dateTime): ?OpeningHoursException
    {
        foreach ($this->exceptions as $exception) {
            if ($exception->contains($dateTime)) {
                return $exception;
            }
        }

        return null;
    }

    public function simplify(): static
    {
        $groups = [];

        foreach ($this->specifications as $specification) {
            $timeRanges = $specification->timeRanges()->simplify();
            $key = $timeRanges->toString();

            $groups[$key] ??= [
                'weekDays' => [],
                'timeRanges' => $timeRanges->toArray(),
            ];

            foreach ($specification->weekDays() as $weekDay) {
                $groups[$key]['weekDays'][] = $weekDay->name();
            }
        }

        return new static(
            specifications: array_values(array_map(
                OpeningHoursSpecification::fromArray(...),
                $groups,
            )),
            exceptions: $this->exceptions,
        );
    }

    public function toString(): string
    {
        return implode('; ', array_map(
            fn (OpeningHoursSpecification $specification) => $specification->toString(),
            $this->specifications,
        ));
    }

    public function toLocaleString(?string $locale = null, array $options = []): string
    {
        return implode('; ', array_map(
            fn (OpeningHoursSpecification $specification) => $specification->toLocaleString($locale, $options),
            $this->specifications,
        ));
    }

    public function toHtml(array $attributes = []): string
    {
        return App::instance()->snippet('schema/opening-hours', [
            'item' => $this,
            'attrs' => $attributes,
        ]);
    }

    public function toArray(): array
    {
        return [
            'specifications' => array_map(
                fn (OpeningHoursSpecification $specification) => $specification->toArray(),
                $this->specifications,
            ),
            'exceptions' => array_map(
                fn (OpeningHoursException $exception) => $exception->toArray(),
                $this->exceptions,
            ),
        ];
    }

    public function toStructuredData(): array
    {
        $graph = [];

        foreach ($this->specifications as $specification) {
            array_push($graph, ...$specification->toStructuredData());
        }

        foreach ($this->exceptions as $exception) {
            $graph[] = $exception->toStructuredData();
        }

        return $graph;
    }
}
