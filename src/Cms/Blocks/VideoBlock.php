<?php

namespace Hks\Schema\Cms\Blocks;

use Hks\Schema\Cms\Concerns\HasVideo;
use Kirby\Cms\Block;
use Kirby\Content\Field;

/**
 * @method Field location()
 * @method Field url()
 * @method Field caption()
 * @method Field ratio()
 * @method Field crop()
 * @method Field autoplay()
 * @method Field muted()
 * @method Field loop()
 * @method Field controls()
 * @method Field preloading()
 * @method Field priority()
 */
class VideoBlock extends Block
{
    use HasVideo;

    public function preload(): Field
    {
        return $this->content()->preloading();
    }
}
