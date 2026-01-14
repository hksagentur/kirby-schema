<?php

namespace Hks\Schema\Cms;

use Kirby\Cms\File;

trait HasVideo
{
    protected ?File $video = null;

    public function hasVideo(): bool
    {
        return $this->video() !== null;
    }

    public function video(): ?File
    {
        return $this->video ??= $this->content()->video()->toFile();
    }

    public function hasVideoPoster(): bool
    {
        return $this->videoPoster() !== null;
    }

    public function videoPoster(): ?File
    {
        return $this->video()?->poster()->toFile();
    }
}
