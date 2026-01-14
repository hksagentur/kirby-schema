<?php

namespace Hks\Schema\Cms\Blocks;

use Hks\Schema\Cms\HasMedia;
use Kirby\Cms\Block;
use Kirby\Cms\Structure;
use Kirby\Content\Field;

class HeroBlock extends Block
{
    use HasMedia;

    public function level(): Field
    {
        return $this->content()->level()->or('h1');
    }

    public function title(): Field
    {
        return $this->content()->title();
    }

    public function eyebrow(): Field
    {
        return $this->content()->eyebrow();
    }

    public function text(): Field
    {
        return $this->content()->text();
    }

    public function actions(): ?Structure
    {
        return $this->content()->actions()->toStructure();
    }
}
