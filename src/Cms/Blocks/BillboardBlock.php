<?php

namespace Hks\Schema\Cms\Blocks;

use Hks\Schema\Cms\Concerns\HasLink;
use Kirby\Cms\Block;
use Kirby\Cms\Blocks;
use Kirby\Content\Field;

/**
 * @method Field title()
 * @method Field text()
 */
class BillboardBlock extends Block
{
    use HasLink;

    public function level(): Field
    {
        return $this->content()->level()->or('h2');
    }

    public function cards(): Blocks
    {
        return $this->content()->cards()->toBlocks();
    }
}
