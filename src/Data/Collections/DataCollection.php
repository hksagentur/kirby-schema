<?php

namespace Hks\Schema\Data\Collections;

use Hks\Schema\Data\Concerns\EnumeratesValues;
use Hks\Schema\Data\Contracts\Enumerable;

/**
 * @template TKey of array-key
 * @template-covariant TValue as object
 *
 * @implements Enumerable<TKey, TValue>
 */
abstract readonly class DataCollection implements Enumerable
{
    /** @use EnumeratesValues<TKey, TValue> */
    use EnumeratesValues;
}
