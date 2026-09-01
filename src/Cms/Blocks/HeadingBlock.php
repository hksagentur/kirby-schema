<?php

namespace Hks\Schema\Cms\Blocks;

use Kirby\Cms\Block;
use Kirby\Content\Field;

/**
 * @method Field text()
 */
class HeadingBlock extends Block
{
    public function level(): Field
    {
        return $this->content()->level()->or('h2');
    }
}
