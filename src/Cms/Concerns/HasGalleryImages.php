<?php

namespace Hks\Schema\Cms\Concerns;

use Kirby\Cms\Files;

trait HasGalleryImages
{
    /** @var array<string, Files> */
    protected array $galleryImages = [];

    public function hasGalleryImages(?string $languageCode = null): bool
    {
        return $this->galleryImages($languageCode)->isNotEmpty();
    }

    public function galleryImages(?string $languageCode = null): ?Files
    {
        $key = $this->kirby()->languageCode($languageCode) ?? 'default';

        return $this->galleryImages[$key] ??= $this->content($languageCode)->gallery()->toFiles();
    }
}
