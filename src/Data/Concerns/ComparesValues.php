<?php

namespace Hks\Schema\Data\Concerns;

trait ComparesValues
{
    public function isEqualTo(self $other): bool
    {
        return $this->equals($other);
    }

    public function isNotEqualTo(self $other): bool
    {
        return $this->notEquals($other);
    }

    abstract public function equals(self $other): bool;
}
