<?php

namespace Hks\Schema\Cms\Blocks;

use Kirby\Cms\Block;
use Kirby\Content\Field;

class HtmlBlock extends Block
{
    public function markup(): Field
    {
        return $this->content()->markup();
    }
}
