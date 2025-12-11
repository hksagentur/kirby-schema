<?php

namespace Hks\Schema\Data;

use Kirby\Toolkit\Str;

readonly class Name extends Text
{
    public function slugify(): Text
    {
        return Text::from(Str::slug($this->value()));
    }
}
