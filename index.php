<?php

use Hks\Schema\Data;
use Hks\Schema\Data\Types;

Kirby::plugin('hksagentur/schema', [
    'options' => [
        'defaultCountry' => 'DE',
        'addressSchemes' => [
            'postalCodeLocality' => [
                '{{ streetAddress }}',
                '{{ postalCode }} {{ addressLocality }}',
                '{{ addressCountry }}',
            ],
            'localityPostalCode' => [
                '{{ streetAddress }}',
                '{{ addressLocality }}',
                '{{ postalCode }}',
                '{{ addressCountry }}',
            ],
            'localityRegionPostalCode' => [
                '{{ streetAddress }}',
                '{{ addressLocality }}, {{ addressRegion }} {{ postalCode }}',
                '{{ addressCountry }}'
            ],
            'localityRegionPostalCodeMultiline' => [
                '{{ streetAddress }}',
                '{{ addressLocality }}',
                '{{ addressRegion }}',
                '{{ postalCode }}',
                '{{ addressCountry }}',
            ],
        ],
        'addressFormats' => [
            'AU' => 'localityRegionPostalCodeMultiline',
            'CA' => 'localityRegionPostalCode',
            'GB' => 'localityPostalCode',
            'IE' => 'localityRegionPostalCodeMultiline',
            'MT' => 'localityPostalCode',
            'US' => 'localityRegionPostalCode',
        ],
        'blueprintAliases' => [],
        'dataTypes' => [
            'distance' => Types\Distance::class,
            'email' => Types\EmailAddress::class,
            'file-url' => Types\FileUrl::class,
            'fraction' => Types\Fraction::class,
            'geo-coordinate' => Types\GeoCoordinate::class,
            'image-url' => Types\ImageUrl::class,
            'length' => Types\Length::class,
            'markdown' => Types\Markdown::class,
            'markup' => Types\Markup::class,
            'opening-hours' => Types\OpeningHours::class,
            'opening-hours-exception' => Types\OpeningHoursException::class,
            'opening-hours-specification' => Types\OpeningHoursSpecification::class,
            'person-name' => Types\PersonName::class,
            'phone-number' => Types\PhoneNumber::class,
            'postal-address' => Types\PostalAddress::class,
            'text' => Types\Text::class,
            'time' => Types\Time::class,
            'time-range' => Types\TimeRange::class,
            'url' => Types\Url::class,
        ],
        'dataObjects' => [
            'organization' => Data\Organization::class,
            'person' => Data\Person::class,
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
