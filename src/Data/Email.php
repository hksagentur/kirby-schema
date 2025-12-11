<?php

namespace Hks\Schema\Data;

use InvalidArgumentException;
use Kirby\Cms\Html;
use Kirby\Toolkit\V;

readonly class Email extends Text
{
    public function toHtml(array $attributes = []): string
    {
        return Html::email($this->value(), attr: $attributes);
    }

    public function toUri(): string
    {
        return sprintf('mailto:%s', $this->value());
    }

    protected function validate(): void
    {
        if (! V::email($this->value())) {
            throw new InvalidArgumentException(sprintf(
                'The given value "%s" does not represent a valid email address.',
                $this->value(),
            ));
        }
    }
}
