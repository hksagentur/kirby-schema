<?php

namespace Hks\Schema\Cms\Blocks;

use Hks\Schema\Cms\HasModelReference;
use Kirby\Cms\Block;
use Kirby\Cms\File;
use Kirby\Cms\Files;
use Kirby\Content\Field;

class CardBlock extends Block
{
    use HasModelReference;

    public function level(): Field
    {
        return $this->content()->level()->or('h3');
    }

    public function eyebrow(): Field
    {
        return $this->content()->eyebrow()->or($this->referencedModel()?->category());
    }

    public function title(): Field
    {
        return $this->content()->title()->or($this->referencedModel()?->title());
    }

    public function text(): Field
    {
        return $this->content()->text()->or($this->referencedModel()?->excerpt());
    }

    public function link(): Field
    {
        return $this->content()->link();
    }

    public function image(): ?File
    {
        return $this->images()->first();
    }

    public function images(): Files
    {
        return $this->content()->image()->or($this->referencedModel()?->cover())->toFiles();
    }
}
