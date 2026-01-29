<?php

namespace Hks\Schema\Data\Contracts;

interface Localizable
{
    /**
     * Convert the object to a localized string representation.
     */
    public function toLocaleString(?string $locale = null): string;
}
