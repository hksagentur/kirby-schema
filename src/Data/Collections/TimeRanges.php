<?php

namespace Hks\Schema\Data\Collections;

use DateTimeInterface;
use Hks\Schema\Data\Concerns\InteractsWithValues;
use Hks\Schema\Data\Types\Time;
use Hks\Schema\Data\Types\TimeRange;
use Hks\Schema\Toolkit\Assert;

/**
 * @extends DataCollection<int, TimeRange>
 */
readonly class TimeRanges extends DataCollection
{
    use InteractsWithValues;

    /** @var array<int, TimeRange> */
    protected array $timeRanges;

    /** @param array<int, TimeRange> $timeRanges */
    public function __construct(array $timeRanges)
    {
        Assert::containsOnly($timeRanges, TimeRange::class);

        $timeRanges = $this->ensureOrder($timeRanges);
        $timeRanges = $this->ensureIndexed($timeRanges);

        $this->timeRanges = $timeRanges;
    }

    public function contains(string|Time|DateTimeInterface $time): bool
    {
        foreach ($this->timeRanges as $timeRange) {
            if ($timeRange->contains($time)) {
                return true;
            }
        }

        return false;
    }

    public function format(int|string|null $timeFormat = null, string $timeSeparator = ' – ', ?string $locale = null): string
    {
        $timeRanges = array_map(
            fn (TimeRange $timeRange) => $timeRange->format($timeFormat, $timeSeparator, $locale),
            $this->timeRanges,
        );

        return implode(', ', $timeRanges);
    }

    public function simplify(): static
    {
        $timeRanges = [];
        $previousTimeRange = null;

        foreach ($this->timeRanges as $timeRange) {
            if ($previousTimeRange?->overlaps($timeRange) || $previousTimeRange?->touches($timeRange)) {
                $timeRange = $previousTimeRange->merge($timeRange);
                array_pop($timeRanges);
            }

            $timeRanges[] = $timeRange;
            $previousTimeRange = $timeRange;
        }

        return new static($timeRanges);
    }

    public function toString(): string
    {
        return implode(', ', array_map(strval(...), $this->timeRanges));
    }

    public function all(): array
    {
        return $this->timeRanges;
    }

    public function toArray(): array
    {
        return array_map(fn (TimeRange $timeRange) => $timeRange->toArray(), $this->timeRanges);
    }
}
