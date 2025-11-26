<?php

namespace Hks\Schema\Plugin;

use Kirby\Cms\App;
use Kirby\Data\Yaml;
use Kirby\Exception\NotFoundException;
use Kirby\Filesystem\F;
use Kirby\Toolkit\A;
use Stringable;

class Blueprint implements Stringable
{
    protected static ?array $aliases = null;

    public function __construct(
        protected string $root
    ) {
    }

    public function exists(): bool
    {
        return F::exists($this->root);
    }

    public function root(): string
    {
        return $this->root;
    }

    public function type(): ?string
    {
        return match ($this->directory()) {
            'blocks' => 'block',
            'fields' => 'field',
            'files' => 'file',
            'pages' => 'page',
            'sections' => 'section',
            'tabs' => 'tab',
            default => null,
        };
    }

    public function name(): string
    {
        return $this->directory() . '/' . F::name($this->root);
    }

    public function directory(): string
    {
        return F::name(F::dirname($this->root));
    }

    public function extension(): string
    {
        return F::extension($this->root);
    }

    public function read(): array
    {
        return App::instance()->apply(
            'hksagentur.schema.blueprint:after',
            [
                'name' => $this->name(),
                'blueprint' => Yaml::read($this->root),
            ],
            'blueprint',
        );
    }

    public function extend(array $props): array
    {
        return A::merge(
            $this->read(),
            $props,
            A::MERGE_REPLACE
        );
    }

    public function __invoke(): array
    {
        return $this->read();
    }

    public function __toString(): string
    {
        return $this->root;
    }

    public function __debugInfo(): array
    {
        return $this->read();
    }

    public static function aliases(): array
    {
        return static::$aliases ??= App::instance()->option('hksagentur.schema.aliases', []);
    }

    public static function alias(string $blueprint): ?string
    {
        $aliases = static::aliases();

        if (in_array($blueprint, $aliases)) {
            return $blueprint;
        }

        if (array_key_exists($blueprint, $aliases)) {
            return $aliases[$blueprint];
        }

        return null;
    }

    public static function create(string $root): static
    {
        return new static($root);
    }

    public static function load(string $directory): array
    {
        $files = [];

        foreach (glob($directory . '/*/*.yml') as $file) {
            $files[] = F::name(F::dirname($file)) . '/' . F::name($file);
        }

        return static::register($directory, $files);
    }

    public static function register(string $directory, array $files): array
    {
        $blueprints = [];

        foreach ($files as $file) {
            $blueprint = static::create($directory . '/' . $file . '.yml');

            if (! $blueprint->exists()) {
                throw new NotFoundException(key: 'blueprint.notFound', data: ['name' => $file]);
            }

            $alias = static::alias($file);

            if ($alias) {
                $blueprints[$alias] = $blueprint;
            }

            $blueprints["@hksagentur/schema/{$file}"] = $blueprint;
        }

        return $blueprints;
    }
}
