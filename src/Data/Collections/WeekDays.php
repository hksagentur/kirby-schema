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
        return in_array($day, $this->days);
    }

    public function startDay(): ?WeekDay
    {
        return A::first($this->days);
    }

    public function endDay(): ?WeekDay
    {
        return A::last($this->days);
    }

    public function toArray(): array
    {
        return $this->days;
    }

    public function toString(): string
    {
        $days = array_map(fn (WeekDay $day) => $day->shortName(), $this->days);

        if ($this->isRange()) {
            return sprintf('%s-%s', A::first($days), A::last($days));
        }

        return implode(', ', $days);
    }

    public function toLocaleString(?string $locale = null, array $options = []): string
    {
        $days = array_map(fn (WeekDay $day) => $day->toLocaleString(locale: $locale, options: $options), $this->days);

        if ($this->isRange()) {
            return sprintf('%s – %s', A::first($days), A::last($days));
        }

        return implode(', ', $days);
    }

    public function __toString(): string
    {
        return $this->toString();
    }
}
