<?php

namespace Hks\Schema\Data\Factory;

use Hks\Schema\Data\Organization;
use Kirby\Content\Content;

class OrganizationFactory
{
    public static function createFromArray(array $attributes): Organization
    {
        return Organization::fromArray($attributes);
    }

    public static function createFromContent(Content $content): Organization
    {
        return Organization::fromArray(array_filter([
            'name' => $content->name()->value(),
            'address' => array_filter([
                'streetAddress' => $content->streetAddress()->value(),
                'postalCode' => $content->postalCode()->value(),
                'locality' => $content->locality()->value(),
                'country' => $content->country()->value(),
            ]),
            'coordinates' => array_filter([
                'latitude' => $content->latitude()->value(),
                'longitude' => $content->longitude()->value(),
            ]),
            'email' => $content->email()->value(),
            'telephone' => $content->telephone()->value(),
            'fax' => $content->fax()->value(),
            'website' => $content->website()->value(),
        ]));
    }
}
