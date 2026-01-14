<?php

namespace Hks\Schema\Cms;

use Kirby\Cms\File;

trait HasMedia
{
    use HasImage;
    use HasVideo;

    public function mediaType(): ?string
    {
        return $this->content()->mediaType()->value() ?: null;
    }

    public function mediaFile(): ?File
    {
        return match ($this->mediaType()) {
            'image' => $this->image(),
            'video' => $this->video(),
            default => null,
        };
    }
}
