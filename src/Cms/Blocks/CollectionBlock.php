<?php

namespace Hks\Schema\Cms\Blocks;

use Kirby\Cms\Block;
use Kirby\Cms\Pages;
use Kirby\Content\Field;

class CollectionBlock extends Block
{
    public function useCurrentPage(): bool
    {
        return $this->parent()->value() === 'current';
    }

    public function useCustomPage(): bool
    {
        return $this->parent()->value() === 'custom';
    }

    public function level(): Field
    {
        return $this->content()->level()->or('h2');
    }

    public function title(): Field
    {
        return $this->content()->title();
    }

    public function text(): Field
    {
        return $this->content()->text();
    }

    public function pages(): Pages
    {
        if ($this->useCurrentPage()) {
            return $this->parent()->children();
        }

        $page = $this->query()->toPage();

        if (! $page) {
            return $page->children();
        }

        return new Pages(parent: $this->parent());
    }
}
