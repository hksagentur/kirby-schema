<?php

namespace Hks\Schema\Cms;

use Kirby\Cms\Files;

trait HasImageGallery
{
    protected ?Files $galleryImages = null;

    public function hasGalleryImages(): bool
    {
        return $this->galleryImages()->isNotEmpty();
    }

    public function galleryImages(): ?Files
    {
        return $this->galleryImages ??= $this->content()->gallery()->toFiles();
    }
}
