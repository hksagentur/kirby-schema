<?php

namespace Hks\Schema\Cms;

use Kirby\Cms\ModelWithContent;
use Kirby\Content\Content;
use Kirby\Uuid\Uuid;

trait HasAction
{
    protected ?Content $link = null;
    protected ?ModelWithContent $target = null;

    public function hasLink(): bool
    {
        return $this->link()->url()->isNotEmpty();
    }

    public function link(): Content
    {
        return $this->link ??= $this->content()->link()->toObject();
    }

    public function hasTarget(): bool
    {
        return $this->target() !== null;
    }

    public function target(): ?ModelWithContent
    {
        return $this->target ??= $this->resolveTarget($this->link()->url()->value());
    }

    protected function resolveTarget(?string $uuid): ?ModelWithContent
    {
        if (! $uuid || ! Uuid::is($uuid)) {
            return null;
        }

        $model = Uuid::for($uuid)?->model();

        if (! $model) {
            return null;
        }

        return $model;
    }
}
