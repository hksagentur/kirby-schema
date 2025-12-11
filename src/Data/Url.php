<?php

namespace Hks\Schema\Data;

use InvalidArgumentException;
use Kirby\Cms\Html;
use Kirby\Http\Uri;
use Kirby\Toolkit\V;

readonly class Url extends Text
{
    public function toHtml(array $attributes = []): string
    {
        return Html::link($this->value(), attr: $attributes);
    }

    public function toUri(): Uri
    {
        return new Uri($this->value());
    }

    protected function validate(): void
    {
        if (! V::url($this->value())) {
            throw new InvalidArgumentException(sprintf(
                'The given value "%s" does not represent a valid URL.',
                $this->value(),
            ));
        }
    }
}
