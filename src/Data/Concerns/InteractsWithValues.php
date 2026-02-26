<?php

namespace Hks\Schema\Data\Concerns;

use Hks\Schema\Data\Contracts\Comparable;

trait InteractsWithValues
{
    protected function ensureType(array $items, string $type): array
    {
        return array_filter($items, fn ($item) => $item instanceof $type);
    }

    protected function ensureUniqueness(array $items): array
    {
        return array_unique($items, SORT_REGULAR);
    }

    protected function ensureIndexed(array $items): array
    {
        return array_values($items);
    }

    protected function ensureOrder(array $items, int|string $direction = SORT_ASC): array
    {
        $direction = match ($direction) {
            SORT_ASC, 'asc', 'ASC' => SORT_ASC,
            SORT_DESC, 'desc', 'DESC' => SORT_DESC,
        };

        usort($items, function (mixed $a, mixed $b) use ($direction) {
            $comparison = match (true) {
                $a instanceof Comparable && $b instanceof Comparable => $a->compare($b),
                is_string($a) && is_string($b) => strnatcmp($a, $b),
                default => $a <=> $b,
            };

            return $direction === SORT_DESC ? -$comparison : $comparison;
        });

        return $items;
    }
}
