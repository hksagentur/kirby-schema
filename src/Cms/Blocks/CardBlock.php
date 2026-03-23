<?php

namespace Hks\Schema\Cms\Blocks;

use Hks\Schema\Cms\HasMedia;
use Kirby\Cms\Block;
use Kirby\Content\Field;

class CardBlock extends Block
{
    use HasMedia;

    public function level(): Field
    {
        return $this->content()->level()->or('h3');
    }

    public function eyebrow(): Field
    {
        return $this->content()->eyebrow()->category();
    }

    public function title(): Field
    {
        return $this->content()->title()->title();
    }

    public function text(): Field
    {
        return $this->content()->text()->excerpt();
    }

    public function link(): Field
    {
        return $this->content()->link();
    }
}
