<?php

namespace Hks\Schema\Cms\Concerns;

use Hks\Schema\Cms\Contracts\HasKeyVisual as HasKeyVisualContract;
use Kirby\Cms\Block;
use Kirby\Cms\Blocks;
use Kirby\Cms\File;
use Kirby\Cms\Files;

trait HasKeyVisual
{
    abstract public function blocks(): Blocks;

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
        if ($block instanceof HasKeyVisualContract) {
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
