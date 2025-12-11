<?php

namespace Hks\Schema\Transformer;

use Closure;
use Kirby\Cms\App;
use Kirby\Toolkit\A;

/**
 * @template TResource of object
 */
abstract class Transformer
{
    protected static ?App $kirby = null;

    protected array $options = [];

    public function __construct(array $options = [])
    {
        $this->options = A::merge(
            $this->defaults(),
            $options,
            A::MERGE_OVERWRITE,
        );
    }

    public function defaults(): array
    {
        return [];
    }

    public function options(): array
    {
        return $this->options;
    }

    public function option(string $key): mixed
    {
        return A::get($this->options(), $key);
    }

    public function kirby(): App
    {
        return static::$kirby ??= App::instance();
    }

    /**
     * @param TResource $resource
     */
    abstract public function transform(object $resource): array;
}
