<?php

namespace Hks\Schema\Cms\Blocks;

use Hks\Schema\Cms\Concerns\HasMediaFile;
use Hks\Schema\Cms\Contracts\HasKeyVisual;
use Hks\Schema\Cms\Concerns\HasLink;
use Kirby\Cms\Block;
use Kirby\Cms\File;
use Kirby\Content\Field;

/**
 * @method Field title()
 * @method Field eyebrow()
 * @method Field text()
 * @method Field reverse()
 */
class MediaTextBlock extends Block implements HasKeyVisual
{
    use HasLink;
    use HasMediaFile;

    public function keyVisual(): ?File
    {
        return match ($this->mediaType()) {
            'video' => $this->poster(),
            'image' => $this->image(),
            default => null,
        };
    }

    public function level(): Field
    {
        return $this->content()->level()->or('h3');
    }

    public function alignment(): Field
    {
        return $this->content()->alignment()->or('normal');
    }
}
