<?php

namespace Hks\Schema\Data\Concerns;

use Kirby\Cms\File;
use Kirby\Cms\Page;
use Kirby\Cms\User;
use Kirby\Uuid\Identifiable;
use Kirby\Uuid\Uuid;

trait TransformsUuid
{
    abstract public function uri(): ?string;

    public function isUuid(): bool
    {
        return Uuid::is($this->uri());
    }

    public function model(): ?Identifiable
    {
        return $this->isUuid() ? Uuid::for($this->uri())?->model() : null;
    }

    public function page(): ?Page
    {
        $model = $this->model();

        if ($model instanceof Page) {
            return $model;
        }

        return null;
    }

    public function file(): ?File
    {
        $model = $this->model();

        if ($model instanceof File) {
            return $model;
        }

        return null;
    }

    public function user(): ?User
    {
        $model = $this->model();

        if ($model instanceof User) {
            return $model;
        }

        return null;
    }
}
