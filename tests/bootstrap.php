<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../index.php';

new Kirby\Cms\App([
    'roots' => [
        'index' => __DIR__,
    ],
]);
