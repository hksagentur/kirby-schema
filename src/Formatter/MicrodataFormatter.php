<?php

namespace Hks\Schema\Formatter;

use Kirby\Cms\Html;

/**
 * @template TValue
 * @extends StructuredDataFormatter<TValue>
 */
abstract class MicrodataFormatter extends StructuredDataFormatter
{
    abstract public function getSchemaType(): string;

    public function defaults(): array
    {
        return array_merge(parent::defaults(), [
            'as' => 'div',
            'attributes' => [],
        ]);
    }

    public function compose(array $content): string
    {
        return Html::tag($this->option('as'), $content, [
            ...$this->option('attributes'),
            'itemscope' => '',
            'itemtype' => $this->getSchemaType(),
        ]);
    }
}
