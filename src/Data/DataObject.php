<?php

namespace Hks\Schema\Data;

use Hks\Schema\Data\Contracts\Arrayable;
use Hks\Schema\Data\Contracts\Htmlable;
use Hks\Schema\Data\Contracts\Schemable;

abstract readonly class DataObject implements Arrayable, Htmlable, Schemable
{
    abstract public static function fromArray(array $value): static;
}
