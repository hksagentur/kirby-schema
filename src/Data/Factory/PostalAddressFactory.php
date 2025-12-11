<?php

namespace Hks\Schema\Data\Factory;

use Hks\Schema\Data\PostalAddress;
use Kirby\Content\Content;

class PostalAddressFactory
{
    public static function createFromArray(array $attributes): PostalAddress
    {
        return PostalAddress::fromArray($attributes);
    }

    public static function createFromContent(Content $content): PostalAddress
    {
        return PostalAddress::fromArray(array_filter([
            'streetAddress' => $content->streetAddress()->value(),
            'postalCode' => $content->postalCode()->value(),
            'locality' => $content->locality()->value(),
            'counrry' => $content->counrry()->value(),
        ]));
    }
}
