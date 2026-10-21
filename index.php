<?php

use Hks\Schema\Data;
use Hks\Schema\Data\Types;

Kirby::plugin('hksagentur/schema', [
    'options' => [
        'defaultCountry' => 'DE',
        'defaultCurrency' => 'EUR',
        'addressFormats' => [],
        'phoneNumberFormats' => [],
        'blueprintAliases' => [],
        'dataTypes' => [
            'currency' => Types\Currency::class,
            'distance' => Types\Distance::class,
            'email' => Types\EmailAddress::class,
            'fraction' => Types\Fraction::class,
            'geo-coordinate' => Types\GeoCoordinate::class,
            'length' => Types\Length::class,
            'markdown' => Types\Markdown::class,
            'markup' => Types\Markup::class,
            'money' => Types\Money::class,
            'opening-hours' => Types\OpeningHours::class,
            'person-name' => Types\PersonName::class,
            'phone-number' => Types\PhoneNumber::class,
            'postal-address' => Types\PostalAddress::class,
            'time' => Types\Time::class,
            'time-range' => Types\TimeRange::class,
            'url' => Types\Url::class,
        ],
        'dataObjects' => [
            'link' => Data\Link::class,
            'organization' => Data\Organization::class,
            'person' => Data\Person::class,
            'place' => Data\Place::class,
        ],
    ],
    'blueprints' => require __DIR__ . '/config/blueprints.php',
    'snippets' => require __DIR__ . '/config/snippets.php',
    'translations' => require __DIR__ . '/config/translations.php',
    'hooks' => require __DIR__ . '/config/hooks.php',
    'collectionMethods' => require __DIR__ . '/config/methods/collection.php',
    'fieldMethods' => require __DIR__ . '/config/methods/field.php',
    'pageMethods' => require __DIR__ . '/config/methods/page.php',
    'structureObjectMethods' => require __DIR__ . '/config/methods/structure-object.php',
]);
