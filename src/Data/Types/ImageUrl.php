<?php

namespace Hks\Schema\Data\Types;

use Kirby\Cms\Html;

readonly class ImageUrl extends FileUrl
{
    public function toHtml(array $attributes = []): string
    {
        return Html::img($this->value(), attr: $attributes);
    }

    public function toLink(array $attributes = []): string
    {
        return Html::link($this->value(), [$this->toHtml()], [
            'type' => $this->mime(),
        ] + $attributes);
    }
}
