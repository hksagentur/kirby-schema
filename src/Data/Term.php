<?php

namespace Hks\Schema\Data;

use Hks\Schema\Formatter\TermHtmlFormatter;
use Hks\Schema\Formatter\TermSchemaTransformer;
use Hks\Schema\Formatter\TermTextFormatter;

readonly class Term extends DataObject
{
    public function __construct(
        public Name $name,
        public ?Text $slug = null,
        public ?Markup $description = null,
    ) {
    }

    public static function fromString(string $value): static
    {
        return new static(Name::from($value));
    }

    public static function fromArray(array $attributes): static
    {
        return new static(
            name: Name::from($attributes['name']),
            slug: Text::tryFrom($attributes['slug'] ?? null),
            description: Markup::tryFrom($attributes['description'] ?? null),
        );
    }

    public function name(): Name
    {
        return $this->name;
    }

    public function slug(): Text
    {
        return $this->slug ??= $this->name()->slugify();
    }

    public function description(): ?Markup
    {
        return $this->description;
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name()->toString(),
            'slug' => $this->slug()?->toString(),
            'description' => $this->description()?->toString(),
        ];
    }

    public function toString(): string
    {
        return $this->format(TermTextFormatter::class);
    }

    public function toHtml(array $attributes = []): string
    {
        return $this->format(TermHtmlFormatter::class, ['attributes' => $attributes]);
    }

    public function toSchema(): array
    {
        return $this->transform(TermSchemaTransformer::class);
    }
}
