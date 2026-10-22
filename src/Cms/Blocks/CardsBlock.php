<?php

namespace Hks\Schema\Cms\Blocks;

use Hks\Schema\Cms\Concerns\HasLink;
use Kirby\Cms\Block;
use Kirby\Cms\Blocks;
use Kirby\Content\Field;

/**
 * @method Field level()
 * @method Field title()
 * @method Field eyebrow()
 * @method Field text()
 */
class CardsBlock extends Block
{
    use HasLink;

    public function cards(): Blocks
    {
        return $this->content()->cards()->toBlocks();
    }
}
