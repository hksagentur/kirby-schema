<?php

namespace Hks\Schema\Data;

use Hks\Schema\Formatter\RoleHtmlFormatter;
use Hks\Schema\Formatter\RoleSchemaTransformer;
use Hks\Schema\Formatter\RoleTextFormatter;

readonly class Role extends DataObject
{
    public function __construct(
        public Name $name,
        public ?Text $slug = null,
        /** @param ?DataCollection<Term> */
        public ?DataCollection $responsibilities = null,
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
            responsibilities: DataCollection::from(array_map(Term::fromString(...), $attributes['responsibilities'] ?? [])),
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

    public function responsibilities(): DataCollection
    {
        return $this->responsibilities ?? DataCollection::empty();
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name->toString(),
            'slug' => $this->slug?->toString(),
            'responsibilities' => $this->responsibilities?->toArray(),
        ];
    }

    public function toString(): string
    {
        return $this->format(RoleTextFormatter::class);
    }

    public function toHtml(array $attributes = []): string
    {
        return $this->format(RoleHtmlFormatter::class, ['attributes' => $attributes]);
    }

    public function toSchema(): array
    {
        return $this->transform(RoleSchemaTransformer::class);
    }
}
