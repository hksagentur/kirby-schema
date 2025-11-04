<?php

namespace Hks\Schema\Cms;

use Kirby\Cms\File;
use Kirby\Cms\Page;
use Kirby\Uuid\Uuid;
use Throwable;

trait HasModelReference
{
    public function hasModelReference(): bool
    {
        return $this->referencedModel() !== null;
    }

    public function referencedModel(): Page|File|null
    {
        $type = $this->referencedModelType();
        $uri = $this->referencedModelId();

        if (! $uri || ! Uuid::is($uri, $type)) {
            return null;
        }

        return Uuid::for($uri)->model();
    }

    public function referencedModelType(): string|array|null
    {
        return ['page', 'file'];
    }

    public function referencedModelId(): string|null
    {
        if (! $this->content()->has('link')) {
            return null;
        }

        $field = $this->content()->link();

        if ($field->isEmpty()) {
            return null;
        }

        try {
            $content = $field->toObject();
        } catch (Throwable) {
            return null;
        }

        if (! $content->has('url')) {
            return null;
        }

        $url = $content->url()->value();

        if (empty($url)) {
            return null;
        }

        return $url;
    }
}
