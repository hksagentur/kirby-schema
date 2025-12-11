<?php

namespace Hks\Schema\Formatter;

use Hks\Schema\Data\Fraction;
use Kirby\Cms\Html;

/**
 * @extends Formatter<Fraction>
 */
class FractionHtmlFormatter extends Formatter
{
    public function defaults(): array
    {
        return [
            'attributes' => [],
        ];
    }

    public function format(mixed $fraction): string
    {
        return Html::tag('data', $fraction->toString(), [
            ...$this->option('attributes'),
            'value' => $fraction->toFloat(),
        ]);
    }
}
