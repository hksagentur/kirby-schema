<?php

namespace Hks\Schema\Cms;

use Kirby\Cms\File;

trait HasImage
{
    protected ?File $image = null;

    public function hasImage(): bool
    {
        return $this->image() !== null;
    }

    public function image(): ?File
    {
        return $this->image ??= $this->content()->image()->toFile();
    }
}
