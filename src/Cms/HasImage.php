<?php

namespace Hks\Schema\Cms;

use Kirby\Cms\File;
use Kirby\Cms\Files;

trait HasImage
{
    protected ?Files $images = null;

    public function hasImages(): bool
    {
        return $this->images()->isNotEmpty();
    }

    public function images(): Files
    {
        return $this->images ??= $this->content()->image()->toFiles();
    }

    public function hasImage(): bool
    {
        return $this->image() !== null;
    }

    public function image(): ?File
    {
        return $this->images()->first();
    }
}
