<?php

namespace Hks\Schema\Cms\Blocks;

use Hks\Schema\Cms\Contracts\HasKeyVisual;
use Hks\Schema\Cms\Concerns\HasMediaFile;
use Kirby\Cms\Block;
use Kirby\Cms\File;
use Kirby\Cms\Structure;
use Kirby\Content\Field;

/**
 * @method Field level()
 * @method Field title()
 * @method Field eyebrow()
 * @method Field text()
 */
class HeroBlock extends Block implements HasKeyVisual
{
    use HasMediaFile;

    public function keyVisual(): ?File
    {
        return match ($this->mediaType()) {
            'video' => $this->poster(),
            'image' => $this->image(),
            default => null,
        };
    }

    public function actions(): ?Structure
    {
        return $this->content()->actions()->toStructure();
    }
}
