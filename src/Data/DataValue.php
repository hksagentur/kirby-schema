<?php

namespace Hks\Schema\Data;

use Hks\Schema\Formatter\Formatter;
use JsonSerializable;
use Kirby\Cms\Html;
use Stringable;

abstract readonly class DataValue implements JsonSerializable, Stringable
{
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

    abstract public function toString(): string;

    public function toHtml(array $attributes = []): string
    {
        return Html::tag('span', $this->toString(), $attributes);
    }

    public function toJson(int $options = 0): string
    {
        return json_encode($this, $options);
    }

    public function jsonSerialize(): mixed
    {
        return $this->toString();
    }

    public function __toString(): string
    {
        return $this->toString();
    }
}
