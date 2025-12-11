<?php

namespace Hks\Schema\Formatter;

use Kirby\Cms\Html;
use Kirby\Toolkit\A;

/**
 * @template TValue
 * @extends StructuredDataFormatter<TValue>
 */
abstract class MicroformatFormatter extends StructuredDataFormatter
{
    abstract public function getMicroformatType(): string;

    public function defaults(): array
    {
        return array_merge(parent::defaults(), [
            'as' => 'div',
            'attributes' => [],
        ]);
    }

    public function compose(array $content): string
    {
        $attributes = $this->option('attributes');

        return Html::tag($this->option('as'), $content, [
            ...$attributes,
            'class' => [
                $this->getMicroformatType(),
                ...A::wrap(A::get($attributes, 'class')),
            ],
        ]);
    }
}
