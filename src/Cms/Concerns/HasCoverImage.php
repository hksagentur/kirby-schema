<?php

namespace Hks\Schema\Cms\Concerns;

use Kirby\Cms\File;

trait HasCoverImage
{
    /** @var array<string, File|null> */
    protected array $coverImage = [];

    public function hasCoverImage(?string $languageCode = null): bool
    {
        return $this->coverImage($languageCode) !== null;
    }

    public function coverImage(?string $languageCode = null): ?File
    {
        $key = $this->kirby()->languageCode($languageCode) ?? 'default';

        if (! array_key_exists($key, $this->coverImage)) {
            $this->coverImage[$key] = $this->content($languageCode)->cover()->toFile();
        }

        return $this->coverImage[$key];
    }
}
