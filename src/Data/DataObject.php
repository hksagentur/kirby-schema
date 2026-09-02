<?php

namespace Hks\Schema\Data;

use Hks\Schema\Data\Contracts\Arrayable;
use Hks\Schema\Data\Contracts\Htmlable;
use Hks\Schema\Data\Contracts\Schemable;
use Stringable;

abstract readonly class DataObject implements Arrayable, Htmlable, Schemable, Stringable
{
    abstract public static function fromArray(array $value): static;

    public function toString(): string
    {
        return $this->toHtml();
    }

    public function __toString(): string
    {
        return $this->toString();
    }
}
