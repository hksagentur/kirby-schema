<?php

namespace Hks\Schema\Data\Types;

use Hks\Schema\Data\Concerns\TransformsStrings;
use Kirby\Cms\App;

readonly class Markdown extends StringType
{
    use TransformsStrings;

    public function toHtml(array $attributes = []): string
    {
        return App::instance()->kirbytext($this->value());
    }
}
