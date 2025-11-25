<?php

namespace Hks\Schema\Cms\Blocks;

use Kirby\Cms\Block;
use Kirby\Cms\Structure;

class TabsBlock extends Block
{
    public function tabs(): Structure
    {
        return $this->content()->tabs()->toStructure();
    }
}
