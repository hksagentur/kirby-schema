<?php

namespace Hks\Schema\Data\Factory;

use Hks\Schema\Data\Term;
use Kirby\Content\Content;

class TermFactory
{
    public static function createFromArray(array $attributes): Term
    {
        return Term::fromArray($attributes);
    }

    public static function createFromContent(Content $content): Term
    {
        return Term::fromArray(array_filter([
            'name' => $content->name()->value(),
            'slug' => $content->slug()->or($content->name())->slug(),
            'description' => $content->description()->value(),
        ]));
    }
}
