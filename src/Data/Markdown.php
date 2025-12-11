<?php

namespace Hks\Schema\Data;

use Hks\Schema\Formatter\MarkdownFormatter;

readonly class Markdown extends Text
{
    public function toHtml(array $options = []): string
    {
        return $this->format(MarkdownFormatter::class, $options);
    }
}
