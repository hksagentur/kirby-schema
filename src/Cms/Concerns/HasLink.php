<?php

namespace Hks\Schema\Cms\Concerns;

use Hks\Schema\Data\Link;
use Kirby\Cms\ModelWithContent;

trait HasLink
{
    protected ?Link $link = null;

    public function hasLink(): bool
    {
        return (bool) $this->link()?->uri();
    }

    public function link(): ?Link
    {
        return $this->link ??= $this->content()->link()->toDataObject('link');
    }

    public function hasTarget(): bool
    {
        return $this->target() !== null;
    }

    public function target(): ?ModelWithContent
    {
        $model = $this->link()?->model();

        if (! ($model instanceof ModelWithContent)) {
            return null;
        }

        return $model;
    }
}
