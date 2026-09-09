<?php

namespace Hks\Schema\Data\Contracts;

interface Schemable
{
    /**
     * Convert the object to a schema.org structured data representation.
     *
     * @return array<string, mixed>|list<array<string, mixed>>
     */
    public function toStructuredData(): array;
}
