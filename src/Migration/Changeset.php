<?php

namespace Hks\Schema\Migration;

use Closure;
use Kirby\Cms\Block;
use Kirby\Cms\Blocks;
use Kirby\Cms\ModelWithContent;
use Kirby\Cms\StructureObject;
use Kirby\Content\Content;
use Kirby\Content\Field;
use Kirby\Data\Data;
use Kirby\Toolkit\A;

readonly class Changeset
{
    /**
     * @param array<string, mixed> $values
     * @param array<string, string> $renamed
     * @param string[] $removed
     */
    protected function __construct(
        protected Content $content,
        protected array $values = [],
        protected array $renamed = [],
        protected array $removed = [],
    ) {
    }

    public static function of(Content $content): static
    {
        return new static($content);
    }

    public function isEmpty(): bool
    {
        return $this->values === [] && $this->removed === [];
    }

    public function isNotEmpty(): bool
    {
        return ! $this->isEmpty();
    }

    public function model(): ?ModelWithContent
    {
        return $this->content->parent();
    }

    public function content(): Content
    {
        return $this->content;
    }

    /**
     * @return string[]
     */
    public function dirty(): array
    {
        return array_values(array_unique([
            ...$this->removed,
            ...array_keys($this->values),
        ]));
    }

    public function set(string $field, mixed $value): static
    {
        $field = strtolower($field);
        $removed = array_values(array_diff($this->removed, [$field]));

        if ($this->isChanged($field, $value)) {
            return $this->with(values: [...$this->values, $field => $value], removed: $removed);
        }

        return $this->with(values: A::without($this->values, $field), removed: $removed);
    }

    public function remove(string $field): static
    {
        $field = strtolower($field);

        return $this->with(
            values: A::without($this->values, $field),
            removed: array_values(array_unique([...$this->removed, $field])),
        );
    }

    /**
     * @param ?Closure(mixed $value): mixed $map
     */
    public function rename(string $from, string $to, ?Closure $map = null): static
    {
        if (! $this->content->has($from)) {
            return $this;
        }

        $from = strtolower($from);
        $to = strtolower($to);

        $value = $this->content->get($from)->value();

        $changeset = $this->remove($from)->set(
            field: $to,
            value: $map ? $map($value) : $value
        );

        return $changeset->with(renamed: [...$this->renamed, $from => $to]);
    }

    /**
     * @param Closure(Field $field): mixed $callback
     */
    public function transform(string $field, Closure $callback): static
    {
        return $this->set($field, $callback($this->content->get($field)));
    }

    /**
     * @param Closure(Changeset $changeset, StructureObject $row): Changeset $callback
     */
    public function structure(string $field, Closure $callback): static
    {
        $field = $this->content->get($field);

        if ($field->isEmpty()) {
            return $this;
        }

        return $this->each($field, $field->toStructure(), $callback, fn (StructureObject $row, Changeset $changes) => $changes->apply());
    }

    /**
     * @param Closure(Changeset $changeset): Changeset $callback
     */
    public function object(string $field, Closure $callback): static
    {
        $field = $this->content->get($field);

        if ($field->isEmpty()) {
            return $this;
        }

        $changes = $callback(static::of($field->toObject()));

        if ($changes->isEmpty()) {
            return $this;
        }

        return $this->set($field->key(), $changes->apply());
    }

    /**
     * @param Closure(Changeset $changeset, Block $block): Changeset $callback
     */
    public function blocks(string $field, Closure $callback): static
    {
        $field = $this->content->get($field);

        if ($field->isEmpty()) {
            return $this;
        }

        return $this->each($field, $this->asBlocks($field), $callback, fn (Block $block, Changeset $changes) => [
            ...$block->toArray(),
            'content' => $changes->apply(),
        ]);
    }

    /**
     * @param Closure(Changeset $changeset, Block $block): Changeset $callback
     */
    public function layouts(string $field, Closure $callback): static
    {
        $field = $this->content->get($field);

        if ($field->isEmpty()) {
            return $this;
        }

        $layouts = [];
        $changed = false;

        foreach ($field->toLayouts() as $layout) {
            $columns = [];

            foreach ($layout->columns() as $column) {
                $blocks = [];

                foreach ($column->blocks(includeHidden: true) as $block) {
                    $changes = $callback(static::of($block->content()), $block);
                    $changed = $changed || $changes->isNotEmpty();

                    $blocks[] = [...$block->toArray(), 'content' => $changes->apply()];
                }

                $columns[] = [...$column->toArray(), 'blocks' => $blocks];
            }

            $layouts[] = [...$layout->toArray(), 'columns' => $columns];
        }

        return $changed ? $this->set($field->key(), $layouts) : $this;
    }

    /**
     * @return array<string, mixed>
     */
    public function apply(): array
    {
        return $this->patch($this->content->toArray(), $this->values);
    }

    /**
     * @param array<string, mixed> $fields
     */
    public function applyTo(array $fields): array
    {
        return $this->patch($fields, A::without($this->toArray(), $this->removed));
    }

    /**
     * Returns the changed fields, encoded for storage.
     *
     * Removed fields are `null`; blocks and layouts are JSON, other nested values YAML.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $data = [
            ...array_fill_keys($this->removed, null),
            ...$this->values,
        ];

        foreach ($data as $field => $value) {
            if (is_array($value)) {
                $data[$field] = $this->encode($field, $value);
            }
        }

        return $data;
    }

    protected function isBlank(mixed $value): bool
    {
        return $value === null || $value === '' || $value === [];
    }

    protected function isChanged(string $field, mixed $value): bool
    {
        if (! $this->content->has($field)) {
            return ! $this->isBlank($value);
        }

        $item = $this->content->get($field);

        $current = match (true) {
            is_array($value) && is_string($item->value()) => $item->yaml(),
            default => $item->value(),
        };

        return $current !== $value;
    }

    /**
     * @param ?array<string, mixed> $values
     * @param ?array<string, string> $renamed
     * @param ?string[] $removed
     */
    protected function with(?array $values = null, ?array $renamed = null, ?array $removed = null): static
    {
        return new static(
            content: $this->content,
            values: $values ?? $this->values,
            renamed: $renamed ?? $this->renamed,
            removed: $removed ?? $this->removed,
        );
    }

    /**
     * Applies values and removals to fields.
     *
     * A renamed field takes the place of its old one, new fields go last.
     *
     * @param array<string, mixed> $fields
     * @param array<string, mixed> $values
     * @return array<string, mixed>
     */
    protected function patch(array $fields, array $values): array
    {
        $result = [];

        foreach ($fields as $field => $value) {
            $to = $this->renamed[$field] ?? null;

            if ($to !== null && array_key_exists($to, $values)) {
                $result[$to] = $values[$to];
            }

            if (! in_array($field, $this->removed, true)) {
                $result[$field] = array_key_exists($field, $values) ? $values[$field] : $value;
            }
        }

        return [...$result, ...$values];
    }

    /**
     * Encodes a nested value for storage.
     *
     * Kirby stores blocks and layouts as JSON and every other field as YAML. The model's blueprint
     * decides, so a field it doesn't define is stored as YAML.
     *
     * @param array<string, mixed> $value
     */
    protected function encode(string $field, array $value): string
    {
        $type = A::get(
            array: $this->model()?->blueprint()->field($field) ?? [],
            key: 'type'
        );

        return match ($type) {
            'blocks', 'layout' => Data::encode($value, 'json'),
            default => Data::encode($value, 'yaml')
        };
    }

    /**
     * @param iterable<Block|StructureObject> $items
     * @param Closure(Block|StructureObject $item, Changeset $changeset): array $serialize
     */
    protected function each(Field $field, iterable $items, Closure $callback, Closure $serialize): static
    {
        $data = [];
        $changed = false;

        foreach ($items as $item) {
            $changes = $callback(static::of($item->content()), $item);
            $changed = $changed || $changes->isNotEmpty();

            $data[] = $serialize($item, $changes);
        }

        return $changed ? $this->set($field->key(), $data) : $this;
    }

    /**
     * Returns all blocks; unlike `$field->toBlocks()` including hidden ones.
     */
    protected function asBlocks(Field $field): Blocks
    {
        return Blocks::factory(Blocks::parse($field->value()), [
            'parent' => $this->model(),
            'field' => $field,
        ]);
    }
}
