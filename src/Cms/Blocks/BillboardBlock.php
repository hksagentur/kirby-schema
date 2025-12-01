<?php

namespace Hks\Schema\Cms\Blocks;

use Kirby\Cms\Block;
use Kirby\Cms\Blocks;
use Kirby\Content\Field;

class BillboardBlock extends Block
{
    public function level(): Field
    {
        return $this->content()->level()->or('h2');
    }

    public function title(): Field
    {
        return $this->content()->title();
    }

    public function text(): Field
    {
        return $this->content()->text();
    }

    public function link(): Field
    {
        return $this->content()->link();
    }

    public function cards(): Blocks
    {
        return $this->content()->cards()->toBlocks();
    }
}
