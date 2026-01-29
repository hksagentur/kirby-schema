<?php

Kirby::plugin('hksagentur/schema', [
    'options' => [
        'defaultCountry' => 'DE',
        'addressSchemes' => [
            'postalCodeLocality' => [
                '{{ streetAddress }}',
                '{{ postalCode }} {{ addressLocality }}',
                '{{ addressCountry }}',
            ],
            'localityRegionPostalCode' => [
                '{{ streetAddress }}',
                '{{ addressLocality }}, {{ addressRegion }} {{ postalCode }}',
                '{{ addressCountry }}'
            ],
            'localityPostalCode' => [
                '{{ streetAddress }}',
                '{{ addressLocality }}',
                '{{ postalCode }}',
                '{{ addressCountry }}',
            ],
        ],
        'addressFormats' => [
            'GB' => 'localityPostalCode',
            'US' => 'localityRegionPostalCode',
        ],
    ],
    'blueprints' => require __DIR__ . '/config/blueprints.php',
    'translations' => require __DIR__ . '/config/translations.php',
    'hooks' => require __DIR__ . '/config/hooks.php',
    'collectionMethods' => require __DIR__ . '/config/methods/collection.php',
    'fieldMethods' => require __DIR__ . '/config/methods/field.php',
    'structureObjectMethods' => require __DIR__ . '/config/methods/structure-object.php',
]);
