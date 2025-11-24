<?php

namespace Hks\Schema\Cms\Blocks;

use Kirby\Cms\Block;
use Kirby\Cms\Files;
use Kirby\Content\Field;

class GalleryBlock extends Block
{
    public function images(): Files
    {
        return $this->content()->images()->toFiles();
    }

    public function caption(): Field
    {
        return $this->content()->caption();
    }

    public function ratio(): Field
    {
        return $this->content()->ratio()->or('auto');
    }

    public function crop(): Field
    {
        return $this->content()->crop();
    }
}
