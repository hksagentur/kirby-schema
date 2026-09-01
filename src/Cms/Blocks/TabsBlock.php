<?php

namespace Hks\Schema\Cms\Blocks;

use Kirby\Cms\Block;
use Kirby\Cms\Structure;
use Kirby\Content\Field;

/**
 * @method Field title()
 * @method Field text()
 */
class TabsBlock extends Block
{
    public function level(): Field
    {
        return $this->content()->level()->or('h2');
    }

    public function tabs(): Structure
    {
        return $this->content()->tabs()->toStructure();
    }
}
