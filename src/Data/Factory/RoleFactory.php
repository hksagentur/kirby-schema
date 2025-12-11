<?php

namespace Hks\Schema\Data\Factory;

use Hks\Schema\Data\Role;
use Kirby\Content\Content;
use Kirby\Content\Field;

class RoleFactory
{
    public static function createFromArray(array $attributes): Role
    {
        return Role::fromArray($attributes);
    }

    public static function createFromContent(Content $content): Role
    {
        return Role::fromArray(array_filter([
            'name' => $content->name()->value(),
            'responsibilities' => $content->responsibilities()
                ->toEntries()
                ->toArray(fn (Field $entry) => $entry->value()),
        ]));
    }
}
