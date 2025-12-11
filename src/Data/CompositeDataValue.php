<?php

namespace Hks\Schema\Data;

use InvalidArgumentException;

abstract readonly class CompositeDataValue extends DataValue
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

    public function jsonSerialize(): mixed
    {
        return $this->toArray();
    }
}
