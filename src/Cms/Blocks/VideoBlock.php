<?php

namespace Hks\Schema\Cms\Blocks;

use Kirby\Cms\Block;
use Kirby\Cms\File;
use Kirby\Content\Field;

class VideoBlock extends Block
{
    protected ?File $video = null;
    protected ?File $poster = null;

    public function location(): Field
    {
        return $this->content()->location();
    }

    public function url(): Field
    {
        return $this->content()->url()->or($this->video()?->url());
    }

    public function video(): ?File
    {
        return $this->video ??= $this->content()->video()->toFile();
    }

    public function poster(): ?File
    {
        return $this->poster ??= $this->content()->poster()->toFile();
    }

    public function caption(): Field
    {
        return $this->content()->caption()->or($this->video()?->caption());
    }

    public function autoplay(): Field
    {
        return $this->content()->autoplay();
    }

    public function muted(): Field
    {
        return $this->content()->muted();
    }

    public function loop(): Field
    {
        return $this->content()->loop();
    }

    public function controls(): Field
    {
        return $this->content()->controls();
    }

    public function preload(): Field
    {
        return $this->content()->preload();
    }
}
