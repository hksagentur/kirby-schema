<?php

namespace Hks\Schema\Data;

use Hks\Schema\Formatter\Formatter;
use Hks\Schema\Transformer\Transformer;
use InvalidArgumentException;
use JsonSerializable;
use Kirby\Cms\Html;
use Stringable;

abstract readonly class DataObject implements JsonSerializable, Stringable
{
    abstract public static function fromArray(array $attributes): static;

    public static function tryFromArray(?array $attributes): ?static
    {
        try {
            return isset($attributes) ? static::fromArray($attributes) : null;
        } catch (InvalidArgumentException) {
            return null;
        }
    }

    /**
     * @template TFormatter of Formatter
     *
     * @param class-string<TFormatter> $formatter
     * @param array $options
     * @return string
     */
    public function format(string $formatter, array $options = []): string
    {
        return (new $formatter($options))->format($this);
    }

    /**
     * @template TTransformer of Transformer
     *
     * @param string|class-string<TTransformer> $transformer
     * @param array $options
     * @return array
     */
    public function transform(string $transformer, array $options = []): array
    {
        return (new $transformer($options))->transform($this);
    }

    abstract public function toArray(): array;

    abstract public function toString(): string;

    public function toHtml(array $attributes = []): string
    {
        return Html::tag('div', $this->toString(), $attributes);
    }

    public function toJson(int $flags = 0): string
    {
        return json_encode($this, $flags) ?: '';
    }

    public function jsonSerialize(): mixed
    {
        return $this->toArray();
    }

    public function __toString(): string
    {
        return $this->toString();
    }
}
