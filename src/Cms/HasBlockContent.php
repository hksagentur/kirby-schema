<?php

namespace Hks\Schema\Cms;

use Hks\Schema\Cms\Contracts\HasKeyVisual;
use Kirby\Cms\Blocks;
use Kirby\Cms\Block;
use Kirby\Cms\Files;
use Kirby\Cms\File;

trait HasBlockContent
{
    protected ?Blocks $blocks = null;

    public function blocks(): Blocks
    {
        return $this->blocks ??= $this->content()->blocks()->toBlocks();
    }

    public function keyVisuals(): Files
    {
        $files = new Files(parent: $this);

        foreach ($this->blocks()->filterBy('priority', 'high') as $block) {
            if ($file = $this->getKeyVisualFromBlock($block)) {
                $files->add($file);
            }
        }

        return $files;
    }

    public function keyVisual(): ?File
    {
        return $this->keyVisuals()->first();
    }

    protected function getKeyVisualFromBlock(Block $block): ?File
    {
        if ($block instanceof HasKeyVisual) {
            return $block->keyVisual();
        }

        return match ($block->type()) {
            'image' => $this->getKeyVisualFromImageBlock($block),
            'video' => $this->getKeyVisualFromVideoBlock($block),
            default => null,
        };
    }

    protected function getKeyVisualFromImageBlock(Block $block): ?File
    {
        return $block->content()->image()->toFile();
    }

    protected function getKeyVisualFromVideoBlock(Block $block): ?File
    {
        $video = $block->content()->video()->toFile();

        if (! $video) {
            return null;
        }

        $poster = $video->content()->poster()->toFile();

        if (! $poster) {
            return null;
        }

        return $poster;
    }
}
