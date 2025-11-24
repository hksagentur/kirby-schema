<?php

namespace Hks\Schema\Cms\Blocks;

use Kirby\Cms\Block;
use Kirby\Cms\Pages;
use Kirby\Content\Field;

class CollectionBlock extends Block
{
    public function useCurrentPage(): bool
    {
        return $this->content()->get('parent')->value() === 'current';
    }

    public function useCustomPage(): bool
    {
        return $this->content()->get('parent')->value() === 'custom';
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

    public function offset(): int
    {
        return $this->content()->offset()->toInt(0);
    }

    public function limit(): int
    {
        return $this->content()->limit()->toInt(-1);
    }

    public function flip(): bool
    {
        return $this->content()->flip()->isTrue();
    }

    public function pages(): Pages
    {
        if ($this->useCurrentPage()) {
            return $this->applyConstraints($this->parent()->children());
        }

        $page = $this->query()->toPage();

        if ($page) {
            return $this->applyConstraints($page->children());
        }

        return new Pages(parent: $this->parent());
    }

    protected function applyConstraints(Pages $pages): Pages
    {
        $offset = $this->offset();

        if ($offset > 0) {
            $pages = $pages->offset($offset);
        }

        $limit = $this->limit();

        if ($limit >= 0) {
            $pages = $pages->paginate($limit);
        }

        $flip = $this->flip();

        if ($flip) {
            $pages = $pages->flip();
        }

        return $pages;
    }
}
