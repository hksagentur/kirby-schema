<?php

namespace Hks\Schema\Data;

use Hks\Schema\Data\Types\DataType;
use Kirby\Cms\App;

class Registry
{
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
        if (is_a($name, $base, true)) {
            return $name;
        }

        $class = App::instance()->option("hksagentur.schema.{$option}.{$name}");

        if (!is_string($class) || !is_a($class, $base, true)) {
            return null;
        }

        return $class;
    }
}
