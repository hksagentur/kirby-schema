<?php

namespace Hks\Schema\Data\Factory;

use Hks\Schema\Data\Person;
use Kirby\Cms\StructureObject;
use Kirby\Content\Content;
use Kirby\Content\Field;

class PersonFactory
{
    public static function createFromArray(array $attributes): Person
    {
        return Person::fromArray($attributes);
    }

    public static function createFromContent(Content $content): Person
    {
        return Person::fromArray(array_filter([
            'givenName' => $content->givenName()->value(),
            'additionalName' => $content->additionalName()->value(),
            'familyName' => $content->familyName()->value(),
            'image' => $content->image()->value(),
            'description' => $content->description()->value(),
            'email' => $content->email()->value(),
            'telephone' => $content->telephone()->value(),
            'fax' => $content->fax()->value(),
            'roles' => $content->roles()->toStructure()->toArray(fn (StructureObject $item) => [
                'name' => $item->name()->value(),
                'responsibilities' => $item->responsibilities()->toEntries()->toArray(fn (Field $entry) => $entry->value()),
            ]),
        ]));
    }
}
