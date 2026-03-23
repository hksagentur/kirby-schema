<?php

namespace Hks\Schema\Cms\Blocks;

use Hks\Schema\Cms\HasAction;
use Kirby\Cms\Block;
use Kirby\Cms\Blocks;
use Kirby\Content\Field;

class CardsBlock extends Block
{
    use HasAction;

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

    public function cards(): Blocks
    {
        return $this->content()->cards()->toBlocks();
    }
}
