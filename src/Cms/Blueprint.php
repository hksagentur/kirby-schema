<?php

namespace Hks\Schema\Cms;

class Blueprint
{
    public static function package(string $namespace): string
    {
        return basename($namespace);
    }

    public static function vendor(string $namespace): ?string
    {
        if (! str_contains($namespace, '/')) {
            return null;
        }

        return ltrim(dirname($namespace), '@');
    }

    public static function namespace(string $namespace, string $directory): array
    {
        $prefix = static::package($namespace);

        $blueprints = [];

        foreach (glob($directory . '/*/*.yml') as $file) {
            $name = basename(dirname($file)) . '/' . basename($file, '.yml');

            $blueprints["{$namespace}/{$name}"] = $file;

            $blueprints["{$prefix}/{$name}"] = [
                'extends' => "{$namespace}/{$name}",
            ];
        }

        return $blueprints;
    }
}
