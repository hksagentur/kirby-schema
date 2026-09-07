<?php

namespace Hks\Schema\Cms\Blocks;

use Kirby\Cms\Block;
use Kirby\Cms\File;
use Kirby\Content\Field;

/**
 * @method Field location()
 * @method Field crop()
 * @method Field autoplay()
 * @method Field muted()
 * @method Field loop()
 * @method Field controls()
 * @method Field preloading()
 * @method Field priority()
 * @method Field ratio()
 */
class VideoBlock extends Block
{
    protected ?File $video = null;

    public function url(): Field
    {
        return $this->content()->url()->or($this->video()?->url());
    }

    public function video(): ?File
    {
        return $this->video ??= $this->content()->video()->toFile();
    }

    public function caption(): Field
    {
        return $this->content()->caption()->or($this->video()?->caption());
    }

    public function preload(): Field
    {
        return $this->content()->preloading();
    }
}
