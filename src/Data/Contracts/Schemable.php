<?php

namespace Hks\Schema\Data\Contracts;

interface Schemable
{
    /**
     * Convert the object to a schema.org representation.
     */
    public function toSchema(): array;
}
