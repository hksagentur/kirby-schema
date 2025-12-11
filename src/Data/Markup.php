<?php

namespace Hks\Schema\Data;

use Kirby\Cms\Html;

readonly class Markup extends Text
{
    public function toPlainText(): string
    {
        return strip_tags($this->toString());
    }

    public function toHtml(array $attributes = []): string
    {
        $content = $this->toString();

        if (! empty($attributes)) {
            return Html::tag('div', $content, $attributes);
        }

        return $content;
    }
}
