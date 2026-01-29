<?php

namespace Hks\Schema\Cms\Concerns;

use Kirby\Cms\File;

trait HasMediaFile
{
    protected ?File $image = null;
    protected ?File $video = null;

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

    public function hasImage(): bool
    {
        return $this->image() !== null;
    }

    public function image(): ?File
    {
        return $this->image ??= $this->content()->image()->toFile();
    }

    public function hasVideo(): bool
    {
        return $this->video() !== null;
    }

    public function video(): ?File
    {
        return $this->video ??= $this->content()->video()->toFile();
    }
}
