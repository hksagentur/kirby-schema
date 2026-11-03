<?php

namespace Hks\Schema\Data;

use Hks\Schema\Data\Concerns\TransformsUuid;
use Hks\Schema\Data\Types\LinkTarget;
use InvalidArgumentException;
use Kirby\Cms\App;
use Kirby\Cms\File;
use Kirby\Cms\Page;

readonly class Link extends DataObject
{
    use TransformsUuid;

    public function __construct(
        public string $uri,
        public ?string $text = null,
        public ?string $icon = null,
        public ?string $rel = null,
        public LinkTarget $target = LinkTarget::Self,
    ) {
        if (empty($uri)) {
            throw new InvalidArgumentException('A link requires a URI.');
        }
    }

    public static function fromArray(array $value): static
    {
        $value = array_filter($value);

        return new static(
            uri: $value['url'] ?? '',
            text: $value['text'] ?? null,
            icon: $value['icon'] ?? null,
            rel: $value['rel'] ?? null,
            target: isset($value['target']) ? LinkTarget::of($value['target']) : LinkTarget::Self,
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

    public function text(): ?string
    {
        return $this->text ?? $this->page()?->title()->value();
    }

    public function icon(): ?string
    {
        return $this->icon;
    }

    public function target(): LinkTarget
    {
        return $this->target;
    }

    public function rel(): ?string
    {
        return $this->rel;
    }

    public function uri(): string
    {
        return $this->uri;
    }

    public function url(): ?string
    {
        $model = $this->model();

        if ($model instanceof Page) {
            return $model->isPublished() ? $model->url() : null;
        }

        if ($model instanceof File) {
            return $model->url();
        }

        return ! $this->isUuid() ? $this->uri : null;
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
            'text' => $this->text,
            'icon' => $this->icon,
            'target' => $this->target->value,
            'rel' => $this->rel,
        ];
    }

    public function toStructuredData(string $type = 'WebPage'): array
    {
        return array_filter([
            '@type' => $type,
            'url' => $this->url(),
            'name' => $this->text(),
        ]);
    }
}
