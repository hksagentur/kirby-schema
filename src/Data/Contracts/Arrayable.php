<?php

namespace Hks\Schema\Data\Contracts;

interface Arrayable
{
    /**
     * Convert the object to an array representation.
     */
    public function toArray(): array;
}
