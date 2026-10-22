<?php

namespace Hks\Schema\Cms\Blocks;

use Kirby\Cms\Block;
use Kirby\Cms\Structure;
use Kirby\Content\Field;

/**
 * @method Field level()
 * @method Field title()
 * @method Field text()
 */
class TabsBlock extends Block
{
    public function tabs(): Structure
    {
        return $this->content()->tabs()->toStructure();
    }
}
