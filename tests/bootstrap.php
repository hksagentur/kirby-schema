<?php

use Kirby\Cms\App;
use Kirby\Cms\Core;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../index.php';

App::$enableWhoops = false;
Core::$indexRoot = '/dev/null';

new App();
