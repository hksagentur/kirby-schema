<?php

namespace Hks\Schema\Data;

use InvalidArgumentException;
use Kirby\Cms\Html;

readonly class PhoneNumber extends Text
{
    public function toHtml(array $attributes = []): string
    {
        return Html::tel($this->value(), attr: $attributes);
    }

    public function toUri(): string
    {
        return sprintf('tel:%s', preg_replace('![^0-9\+]+!', '', $this->value()));
    }

    protected function validate(): void
    {
        if (! preg_match('/^[+]?[0-9 ]{5,15}$/', $this->value())) {
            throw new InvalidArgumentException(sprintf(
                'The given value "%s" does not represent a valid phone number.',
                $this->value(),
            ));
        }
    }
}
