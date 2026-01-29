<?php

namespace Hks\Schema\Cms\Concerns;

use Kirby\Cms\File;

trait HasCoverImage
{
    protected ?File $coverImage = null;

    public function hasCoverImage(): bool
    {
        return $this->coverImage() !== null;
    }

    public function coverImage(): ?File
    {
        return $this->coverImage ??= $this->content()->cover()->toFile();
    }
}
