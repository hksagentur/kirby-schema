<?php

namespace Hks\Schema\Cms\Blocks;

use Kirby\Cms\Block;
use Kirby\Cms\File;
use Kirby\Content\Field;

class MediaTextBlock extends Block
{
    public function level(): Field
    {
        return $this->content()->level()->or('h3');
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

    public function image(): ?File
    {
        return $this->content()->image()->toFile();
    }

    public function link(): Field
    {
        return $this->content()->link();
    }

    public function alignment(): Field
    {
        return $this->content()->alignment()->or('normal');
    }

    public function reverse(): Field
    {
        return $this->content()->reverse();
    }
}
