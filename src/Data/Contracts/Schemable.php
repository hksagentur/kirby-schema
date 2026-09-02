<?php

namespace Hks\Schema\Data\Contracts;

interface Schemable
{
    /**
     * Convert the object to a schema.org structured data representation.
     */
    public function toStructuredData(): array;
}
