<?php

namespace Hks\Schema\Cms;

use Kirby\Cms\File;
use Kirby\Cms\Files;

trait HasVideo
{
    protected ?Files $videos = null;

    public function hasVideos(): bool
    {
        return $this->videos()->isNotEmpty();
    }

    public function videos(): Files
    {
        return $this->videos ??= $this->content()->video()->toFiles();
    }

    public function hasVideo(): bool
    {
        return $this->video() !== null;
    }

    public function video(): ?File
    {
        return $this->videos()->first();
    }

    public function hasPoster(): bool
    {
        return $this->poster() !== null;
    }

    public function poster(): ?File
    {
        return $this->video()?->poster()->toFile();
    }
}
