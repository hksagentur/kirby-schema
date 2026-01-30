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
    ],
    'blueprints' => require __DIR__ . '/config/blueprints.php',
    'snippets' => require __DIR__ . '/config/snippets.php',
    'translations' => require __DIR__ . '/config/translations.php',
    'hooks' => require __DIR__ . '/config/hooks.php',
    'collectionMethods' => require __DIR__ . '/config/methods/collection.php',
    'fieldMethods' => require __DIR__ . '/config/methods/field.php',
    'structureObjectMethods' => require __DIR__ . '/config/methods/structure-object.php',
]);
