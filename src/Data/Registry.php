<?php

namespace Hks\Schema\Data;

use Hks\Schema\Data\Attributes\ArrayInput;
use Hks\Schema\Data\Types\DataType;
use Kirby\Cms\App;
use ReflectionClass;

class Registry
{
    public static function acceptsArrayInput(string $class): bool
    {
        return (new ReflectionClass($class))->getAttributes(ArrayInput::class) !== [];
    }

    public static function dataType(string $name): ?string
    {
        return static::resolve('dataTypes', $name, DataType::class);
    }

    public static function dataObject(string $name): ?string
    {
        return static::resolve('dataObjects', $name, DataObject::class);
    }

    protected static function resolve(string $option, string $name, string $base): ?string
    {
        $class = App::instance()->option("hksagentur.schema.{$option}.{$name}");

        if (!is_string($class) || !is_a($class, $base, true)) {
            return null;
        }

        return $class;
    }
}
