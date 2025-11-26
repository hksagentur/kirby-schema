<?php

namespace Hks\Schema\Cms\Blocks;

use Kirby\Cms\Block;
use Kirby\Cms\Pages;
use Kirby\Content\Field;

class CollectionBlock extends Block
{
    public function useCurrentPage(): bool
    {
        return $this->source()->value() === 'current';
    }

    public function useCustomPage(): bool
    {
        return $this->source()->value() === 'custom';
    }

    public function source(): Field
    {
        return $this->content()->get('parent')->or('current');
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

    public function paginate(): bool
    {
        return $this->content()->paginate()->isTrue();
    }

    public function reverse(): bool
    {
        return $this->content()->reverse()->isTrue();
    }

    public function offset(): int
    {
        return $this->content()->offset()->toInt(0);
    }

    public function limit(): int
    {
        return $this->content()->limit()->toInt(-1);
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
        $reverse = $this->reverse();

        if ($reverse) {
            $pages = $pages->flip();
        }

        $offset = $this->offset();

        if ($offset > 0) {
            $pages = $pages->offset($offset);
        }

        $limit = $this->limit();

        if ($limit >= 0) {
            $pages = $this->paginate() ? $pages->paginate($limit) : $pages->limit($limit);
        }

        return $pages;
    }
}
