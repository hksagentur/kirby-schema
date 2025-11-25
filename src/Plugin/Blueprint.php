<?php

namespace Hks\Schema\Plugin;

use Kirby\Cms\App;
use Kirby\Data\Yaml;
use Kirby\Filesystem\F;
use Kirby\Toolkit\A;
use Stringable;

class Blueprint implements Stringable
{
    protected static ?App $kirby = null;
    protected static ?string $directory = null;

    public function __construct(
        protected string $name
    ) {
    }

    public function kirby(): App
    {
        return static::$kirby ?? App::instance();
    }

    public function directory(): string
    {
        return static::$directory ??= $this->kirby()->plugin('hksagentur/schema')->root() . '/config/blueprints';
    }

    public function exists(): bool
    {
        return F::exists($this->root());
    }

    public function name(): string
    {
        return $this->name;
    }

    public function root(): string
    {
        return $this->directory() . '/' . $this->name() . '.yml';
    }

    public function read(): array
    {
        return $this->kirby()->apply(
            'hksagentur.schema.blueprint:after',
            [
                'name' => $this->name,
                'blueprint' => Yaml::read($this->root()),
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
        return $this->root();
    }

    public static function create(string $name): static
    {
        return new static($name);
    }

    public static function register(array $names): array
    {
        $blueprints = [];

        foreach ($names as $name) {
            $blueprints["@hksagentur/schema/{$name}"] = static::create($name);
        }

        return $blueprints;
    }
}
