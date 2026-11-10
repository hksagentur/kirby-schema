<?php

namespace Hks\Schema\Cms\Concerns;

use Kirby\Cms\Blocks;

trait HasBlocks
{
    /**
     * @var array<string, Blocks>
     */
    protected array $blocks = [];

    public function blocks(?string $languageCode = null): Blocks
    {
        $key = $this->kirby()->languageCode($languageCode) ?? 'default';

        return $this->blocks[$key] ??= $this->content($languageCode)->blocks()->toBlocks();
    }
}
