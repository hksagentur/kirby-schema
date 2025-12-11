<?php

namespace Hks\Schema\Formatter;

use Kirby\Cms\App;
use Kirby\Toolkit\A;

/**
 * @template TValue
 */
abstract class Formatter
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

    public function option(string $key, ?array $default = null): mixed
    {
        return A::get($this->options(), $key, $default);
    }

    public function kirby(): App
    {
        return static::$kirby ??= App::instance();
    }
}
