<?php

namespace Hks\Schema\Data\Collections;

use Hks\Schema\Data\Concerns\InteractsWithValues;
use Hks\Schema\Data\Contracts\Localizable;
use Hks\Schema\Data\Types\WeekDay;
use Hks\Schema\Toolkit\Assert;
use Kirby\Toolkit\A;
use Stringable;

/**
 * @extends DataCollection<int, WeekDay>
 */
readonly class WeekDays extends DataCollection implements Stringable, Localizable
{
    use InteractsWithValues;

    /** @var array<int, WeekDay> */
    protected array $days;

    /** @param array<int, WeekDay> $days */
    public function __construct(array $days)
    {
        Assert::containsOnly($days, WeekDay::class);

        $days = $this->ensureUniqueness($days);
        $days = $this->ensureIndexed($days);
        $days = $this->ensureOrder($days);

        $this->days = $days;
    }

    public function isRange(): bool
    {
        foreach ($this->pairwise() as [$current, $next]) {
            if (! $next->follows($current)) {
                return false;
            }
        }

        return $this->containsMany();
    }

    public function containsOne(): bool
    {
        return count($this->days) === 1;
    }

    public function containsMany(): bool
    {
        return count($this->days) > 1;
    }

    public function contains(WeekDay $day): bool
    {
        return in_array($day, $this->days, true);
    }

    public function startDay(): ?WeekDay
    {
        return A::first($this->days);
    }

    public function endDay(): ?WeekDay
    {
        return A::last($this->days);
    }

    public function all(): array
    {
        return $this->days;
    }

    /**
     * @param callable(WeekDay): string $callback
     */
    public function join(callable $callback, string $rangeSeparator = ' – ', string $listSeparator = ', '): string
    {
        $days = $this->map($callback);

        if ($this->isRange()) {
            return A::first($days) . $rangeSeparator . A::last($days);
        }

        return implode($listSeparator, $days);
    }

    public function format(?string $dayFormat = null, string $daySeparator = ' – ', ?string $locale = null): string
    {
        $options = match (true) {
            $dayFormat !== null => ['style' => $dayFormat],
            default => [],
        };

        return $this->join(
            callback: fn (WeekDay $day) => $day->toLocaleString(locale: $locale, options: $options),
            rangeSeparator: $daySeparator,
        );
    }

    public function toString(): string
    {
        return $this->join(
            callback: fn (WeekDay $day) => $day->shortName(),
            rangeSeparator: '-',
        );
    }

    public function toLocaleString(?string $locale = null, array $options = []): string
    {
        return $this->join(
            callback: fn (WeekDay $day) => $day->toLocaleString(locale: $locale, options: $options),
        );
    }

    public function __toString(): string
    {
        return $this->toString();
    }
}
