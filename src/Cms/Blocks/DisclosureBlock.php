<?php

namespace Hks\Schema\Cms\Blocks;

use Kirby\Cms\Block;
use Kirby\Cms\Blocks;
use Kirby\Cms\Layouts;
use Kirby\Content\Field;

/**
 * @method Field level()
 * @method Field title()
 */
class DisclosureBlock extends Block
{
    public function isOpen(): bool
    {
        return $this->content()->open()->toBool();
    }

    public function isClosed(): bool
    {
        return ! $this->isOpen();
    }

    public function text(): Field
    {
        return new Field(
            parent: $this->parent(),
            key: 'text',
            value: $this->blocks()->toHtml(),
        );
    }

    public function blocks(): Blocks
    {
        return $this->layouts()->toBlocks();
    }

    public function layouts(): Layouts
    {
        return $this->content()->layout()->toLayouts();
    }
}
