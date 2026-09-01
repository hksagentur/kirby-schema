<?php

namespace Hks\Schema\Cms\Blocks;

use Kirby\Cms\Block;
use Kirby\Cms\Files;
use Kirby\Content\Field;

/**
 * @method Field caption()
 * @method Field crop()
 */
class GalleryBlock extends Block
{
    public function ratio(): Field
    {
        return $this->content()->ratio()->or('auto');
    }

    public function images(): Files
    {
        return $this->content()->images()->toFiles();
    }
}
