<?php

namespace Hks\Schema\Formatter;

use Kirby\Cms\App;
use Stringable;

/**
 * @extends Formatter<string|Stringable>
 */
class MarkdownFormatter extends Formatter
{
    public function defaults(): array
    {
        return [
            'inline' => false,
        ];
    }

    public function format(mixed $value): string
    {
        return App::instance()->markdown((string) $value, $this->options());
    }
}
