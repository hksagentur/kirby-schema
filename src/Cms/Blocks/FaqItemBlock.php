<?php

namespace Hks\Schema\Cms\Blocks;

use Kirby\Content\Field;

class FaqItemBlock extends DisclosureBlock
{
    public function level(): Field
    {
        return $this->content()->level()->or('h3');
    }
}
