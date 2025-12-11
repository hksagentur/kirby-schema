<?php

namespace Hks\Schema\Data;

use Kirby\Toolkit\Str;

readonly class Country extends Text
{
    public function toString(): string
    {
        return Str::upper($this->value());
    }
}
