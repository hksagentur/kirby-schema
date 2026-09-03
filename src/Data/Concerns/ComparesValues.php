<?php

namespace Hks\Schema\Data\Concerns;

use Hks\Schema\Data\Types\DataType;

trait ComparesValues
{
    public function isEqualTo(DataType $other): bool
    {
        return $this->equals($other);
    }

    public function isNotEqualTo(DataType $other): bool
    {
        return $this->notEquals($other);
    }

    abstract public function equals(DataType $other): bool;

    public function notEquals(DataType $other): bool
    {
        return ! $this->equals($other);
    }
}
