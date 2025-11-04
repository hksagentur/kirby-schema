<?php

namespace Hks\Schema\Cms\Blocks;

use Hks\Schema\Cms\HasModelReference;
use Kirby\Cms\Block;
use Kirby\Cms\Blocks;
use Kirby\Content\Content;
use Kirby\Content\Field;

class CardsBlock extends Block
{
    use HasModelReference;

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

    public function link(): Content
    {
        return $this->content()->link()->toObject();
    }

    public function cards(): Blocks
    {
        return $this->content()->cards()->toBlocks();
    }
}
