<?php

namespace Hks\Schema\Cms\Blocks;

use Hks\Schema\Cms\Concerns\HasMediaFile;
use Kirby\Cms\Block;
use Kirby\Cms\Structure;
use Kirby\Content\Field;

/**
 * @method Field level()
 * @method Field title()
 * @method Field eyebrow()
 * @method Field text()
 */
class HeroBlock extends Block
{
    use HasMediaFile;

    public function actions(): ?Structure
    {
        return $this->content()->actions()->toStructure();
    }
}
