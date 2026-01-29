<?php

namespace Hks\Schema\Data\Contracts;

interface Htmlable
{
    /**
     * Convert the object to its HTML representation.
     */
    public function toHtml(array $attributes = []): string;
}
