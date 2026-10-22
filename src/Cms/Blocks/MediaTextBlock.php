<?php

namespace Hks\Schema\Cms\Blocks;

use Hks\Schema\Cms\Concerns\HasMediaFile;
use Hks\Schema\Cms\Contracts\HasKeyVisual;
use Hks\Schema\Cms\Concerns\HasLink;
use Kirby\Cms\Block;
use Kirby\Cms\File;
use Kirby\Content\Field;

/**
 * @method Field level()
 * @method Field title()
 * @method Field eyebrow()
 * @method Field text()
 * @method Field alignment()
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
}
