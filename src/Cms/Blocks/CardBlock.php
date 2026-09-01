<?php

namespace Hks\Schema\Cms\Blocks;

use Hks\Schema\Cms\Concerns\HasMediaFile;
use Hks\Schema\Cms\Concerns\HasLink;
use Kirby\Cms\Block;
use Kirby\Content\Field;

class CardBlock extends Block
{
    use HasLink;
    use HasMediaFile;

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
