<?php

namespace Hks\Schema\Data;

use Hks\Schema\Data\Concerns\TransformsUuid;
use Hks\Schema\Data\Types\LinkTarget;
use Kirby\Cms\App;
use Kirby\Cms\File;
use Kirby\Cms\Page;

readonly class Link extends DataObject
{
    use TransformsUuid;

    public function __construct(
        public string $uri,
        public ?string $title = null,
        public ?string $rel = null,
        public LinkTarget $target = LinkTarget::Self,
    ) {
    }

    public static function fromArray(array $value): static
    {
        return new static(
            uri: (string) ($value['url'] ?? ''),
            title: $value['title'] ?? null,
            rel: $value['rel'] ?? null,
            target: LinkTarget::of($value['target'] ?? LinkTarget::default()),
        );
    }

    public function isActive(): bool
    {
        return $this->page()?->isActive() ?? false;
    }

    public function isOpen(): bool
    {
        return $this->page()?->isOpen() ?? false;
    }

    public function uri(): string
    {
        return $this->uri;
    }

    public function title(): ?string
    {
        return $this->title;
    }

    public function rel(): ?string
    {
        return $this->rel;
    }

    public function url(): ?string
    {
        $model = $this->model();

        if (
            $model instanceof Page ||
            $model instanceof File
        ) {
            return $model->url();
        }

        return ! $this->isUuid() ? $this->uri : null;
    }

    public function target(): LinkTarget
    {
        return $this->target;
    }

    public function toHtml(array $attributes = []): string
    {
        return App::instance()->snippet('schema/link', [
            'item' => $this,
            'attrs' => $attributes,
        ]);
    }

    public function toArray(): array
    {
        return [
            'url' => $this->uri,
            'title' => $this->title,
            'target' => $this->target->value,
            'rel' => $this->rel,
        ];
    }

    public function toStructuredData(string $type = 'WebPage'): array
    {
        return array_filter([
            '@type' => $type,
            'url' => $this->url(),
            'name' => $this->title,
        ]);
    }
}
