<?php

namespace Hks\Schema\Integration;

use Kirby\Cms\Blueprint;

trait InteractsWithBlueprintFields
{
    protected static function resolvedBlueprintFields(string $blueprint): array
    {
        $props = Blueprint::find($blueprint);

        return Blueprint::fieldsProps($props['fields'] ?? []);
    }

    protected static function resolvedBlueprintFieldNames(string $blueprint): array
    {
        return array_keys(static::resolvedBlueprintFields($blueprint));
    }

    protected static function contentFromBlueprint(string $blueprint, array $values): array
    {
        return array_intersect_key($values, array_flip(static::resolvedBlueprintFieldNames($blueprint)));
    }
}
