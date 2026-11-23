<?php

use Hks\Schema\Toolkit\Metadata;

$translations = [
    'en' => require dirname(__DIR__) . '/lang/en.php',
    'de' => require dirname(__DIR__) . '/lang/de.php',
];

$resources = [
    'country' => Metadata::countryNames(),
    'currency' => Metadata::currencyNames(),
];

foreach ($resources as $type => $languages) {
    foreach ($languages as $language => $entries) {
        foreach ($entries as $code => $name) {
            $translations[$language]["hksagentur.schema.{$type}.{$code}"] = $name;
        }
    }
}

return $translations;
