<?php

namespace Hks\Schema\Data\Collections;

use Hks\Schema\Data\Concerns\InteractsWithValues;
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

    public function simplify(): static
    {
        throw new \Exception('Not implemented yet.');
    }

    public function toArray(): array
    {
        return $this->timeRanges;
    }

    public function format(int|string|null $timeFormat = null, string $timeSeparator = ' – ', ?string $locale = null): string
    {
        $timeRanges = array_map(
            fn (TimeRange $timeRange) => $timeRange->format($timeFormat, $timeSeparator, $locale),
            $this->timeRanges,
        );

        return implode(', ', $timeRanges);
    }

    public function toString(): string
    {
        $timeRanges = array_map(fn (TimeRange $timeRange) => $timeRange->toString(), $this->timeRanges);

        return implode(', ', $timeRanges);
    }
}
