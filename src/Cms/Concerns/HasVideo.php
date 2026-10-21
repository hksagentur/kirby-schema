<?php

namespace Hks\Schema\Cms\Concerns;

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
}
