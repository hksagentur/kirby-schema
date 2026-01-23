<?php

namespace Hks\Schema\Cms\Blocks;

use Hks\Schema\Cms\Contracts\HasKeyVisual;
use Hks\Schema\Cms\HasMedia;
use Kirby\Cms\Block;
use Kirby\Cms\File;
use Kirby\Content\Field;

class MediaTextBlock extends Block implements HasKeyVisual
{
    use HasMedia;

    public function keyVisual(): ?File
    {
        return match ($this->mediaType()) {
            'video' => $this->video()?->poster()->toFile(),
            'image' => $this->image(),
            default => null,
        };
    }

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
