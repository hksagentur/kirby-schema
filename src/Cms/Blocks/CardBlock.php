<?php

namespace Hks\Schema\Cms\Blocks;

use Hks\Schema\Cms\HasAction;
use Hks\Schema\Cms\HasMedia;
use Kirby\Cms\Block;
use Kirby\Content\Field;

class CardBlock extends Block
{
    use HasAction;
    use HasMedia;

    public function level(): Field
    {
        return $this->content()->level()->or('h3');
    }

    public function title(): Field
    {
        return $this->content()->title()->or($this->target()?->title());
    }

    public function eyebrow(): Field
    {
        return $this->content()->eyebrow()->or($this->target()?->category());
    }

    public function text(): Field
    {
        return $this->content()->text()->or($this->target()?->excerpt());
    }
}
