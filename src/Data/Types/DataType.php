<?php

namespace Hks\Schema\Data\Types;

use Hks\Schema\Data\Contracts\Jsonable;
use JsonSerializable;
use Stringable;

abstract readonly class DataType implements Jsonable, Stringable, JsonSerializable
{
    public function isEqualTo(self $other): bool
    {
        return $this->equals($other);
    }

    public function isNotEqualTo(self $other): bool
    {
        return $this->notEquals($other);
    }

    public function equals(self $other): bool
    {
        if ($this::class !== $other::class) {
            return false;
        }

        if ($this->toString() !== $other->toString()) {
            return false;
        }

        return true;
    }

    public function notEquals(self $other): bool
    {
        return ! $this->equals($other);
    }

    public function toJson(int $options = 0): string|false
    {
        return json_encode($this, $options);
    }

    abstract public function toString(): string;

    public function jsonSerialize(): mixed
    {
        return $this->toString();
    }

    public function __toString(): string
    {
        return $this->toString();
    }
}
