<?php

namespace Hks\Schema\Cms\Concerns;

use Kirby\Cms\Blocks;

trait HasBlocks
{
    protected ?Blocks $blocks = null;

    public function blocks(): Blocks
    {
        return $this->blocks ??= $this->content()->blocks()->toBlocks();
    }
}
