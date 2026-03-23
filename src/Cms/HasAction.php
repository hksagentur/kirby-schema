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
        $uuid = $this->link()->url()->value();

        if (! $uuid) {
            return null;
        }

        return Uuid::for($uuid)?->model();
    }
}
