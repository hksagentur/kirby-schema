<?php

require __DIR__.'/config/helpers.php';

Kirby::plugin('hksagentur/schema', [
    'blueprints' => require __DIR__ . '/config/blueprints.php',
    'translations' => require __DIR__ . '/config/translations.php',
    'hooks' => require __DIR__ . '/config/hooks.php',
    'collectionMethods' => require __DIR__ . '/config/methods/collection.php',
    'fieldMethods' => require __DIR__ . '/config/methods/field.php',
]);
