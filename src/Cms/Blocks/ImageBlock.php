<?php

namespace Hks\Schema\Cms\Blocks;

use Kirby\Cms\Block;
use Kirby\Cms\File;
use Kirby\Cms\FileVersion;
use Kirby\Content\Field;
use Kirby\Image\Dimensions;

class ImageBlock extends Block
{
    protected ?File $image = null;

    public function location(): Field
    {
        return $this->content()->location();
    }

    public function src(): Field
    {
        return $this->content()->src()->or($this->image()?->url());
    }

    public function image(): ?File
    {
        return $this->image ??= $this->content()->image()->toFile();
    }

    public function alt(): Field
    {
        return $this->content()->alt()->or($this->image()?->alt());
    }

    public function caption(): Field
    {
        return $this->content()->caption()->or($this->image()?->caption());
    }

    public function link(): Field
    {
        return $this->content()->link();
    }

    public function ratio(): Field
    {
        return $this->content()->ratio()->or('auto');
    }

    public function crop(): Field
    {
        return $this->content()->crop();
    }

    public function loading(): Field
    {
        return $this->content()->loading();
    }

    public function priority(): Field
    {
        return $this->content()->priority();
    }

    public function thumb(int $containerWidth): File|FileVersion|null
    {
        return $this->image()?->thumb(
            $this->thumbOptions($containerWidth)
        );
    }

    public function thumbOptions(int $containerWidth): array
    {
        /** @var \Kirby\Image\Dimensions|null $dimensions */
        $dimensions = $this->thumbDimensions($containerWidth);

        return match ($this->thumbResizeStrategy()) {
            'cover' => [
                'width' => $dimensions?->width(),
                'height' => $dimensions?->height(),
                'crop' => true,
            ],
            'contain' => [
                'width' => $dimensions?->width(),
                'height' => $dimensions?->height(),
            ],
            default => [
                'width' => $dimensions?->width(),
            ],
        };
    }

    public function thumbDimensions(int $containerWidth): ?Dimensions
    {
        /** @var \Kirby\Image\Dimensions|null $dimensions */
        $dimensions = clone $this->image()?->dimensions();

        return match ($this->thumbResizeStrategy()) {
            'cover' => $dimensions?->crop(
                width: $containerWidth,
                height: round($containerWidth / $this->ratio()->toDecimal()),
            ),
            'contain' => $dimensions?->resize(
                width: $containerWidth,
                height: round($containerWidth / $this->ratio()->toDecimal()),
            ),
            default => $dimensions?->resize(
                width: $containerWidth,
            ),
        };
    }

    public function thumbResizeStrategy(): string
    {
        $ratio = $this->ratio()->value();

        if ($ratio === 'auto') {
            return 'scale-down';
        }

        $crop = $this->crop()->toBool();

        if ($crop) {
            return 'cover';
        }

        return 'contain';
    }
}
