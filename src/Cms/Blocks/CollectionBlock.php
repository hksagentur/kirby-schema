<?php

namespace Hks\Schema\Cms\Blocks;

use Kirby\Cms\App;
use Kirby\Cms\Block;
use Kirby\Cms\Pages;
use Kirby\Content\Content;
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
        return $this->content()
            ->get('parent')
            ->or('current');
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

    public function link(): Content
    {
        return $this->content()->link()->toObject();
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

    public function paginate(): bool
    {
        return $this->content()->paginate()->isTrue();
    }

    public function paginationOptions(): array
    {
        return [
            'method' => $this->paginationMethod(),
            'variable' => $this->paginationVariable(),
            'limit' => $this->limit(),
        ];
    }

    public function paginationMethod(): ?string
    {
        $method = $this->content()->paginationMethod()->value();

        if (! $method) {
            return App::instance()->option('pagination.method');
        }

        return $method;
    }

    public function paginationVariable(): ?string
    {
        $variable = $this->content()->paginationVariable()->value();

        if (! $variable) {
            return App::instance()->option('pagination.variable');
        }

        return $variable;
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

        if ($limit === -1) {
            return $pages;
        }

        $paginate = $this->paginate();

        if ($paginate) {
            return $pages->paginate($this->paginationOptions());
        }

        return $pages->limit($limit);
    }
}
