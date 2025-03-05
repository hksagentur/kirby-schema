<?php

namespace Hks\Schema\Cms;

use Kirby\Cms\File;

trait HasCoverImage
{
    protected ?File $coverImage = null;

    public function coverImage(): ?File
    {
        return $this->coverImage ??= $this->content()->cover()->toFile();
    }
}
