<?php

namespace Hks\Schema\Data;

use Kirby\Cms\Html;

readonly class ImageUrl extends Url
{
    public function toHtml(array $attributes = []): string
    {
        return Html::img($this->value(), attr: [
            'alt' => '',
            'loading' => 'lazy',
            'decoding' => 'async',
            ...$attributes,
        ]);
    }
}
