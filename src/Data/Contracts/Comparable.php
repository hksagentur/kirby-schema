<?php

namespace Hks\Schema\Data\Contracts;

interface Comparable
{
    /**
     * Compare two object instances with each other.
     */
    public function compare(mixed $other): int;
}
