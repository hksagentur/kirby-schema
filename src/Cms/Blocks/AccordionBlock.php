<?php

namespace Hks\Schema\Cms\Blocks;

use Kirby\Cms\Block;
use Kirby\Cms\Blocks;
use Kirby\Content\Field;

/**
 * @method Field level()
 * @method Field title()
 * @method Field text()
 */
class AccordionBlock extends Block
{
    public function items(): Blocks
    {
        return $this->content()->items()->toBlocks();
    }
}
