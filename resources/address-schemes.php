<?php

/**
 * Address templates adapted from Google's address metadata.
 *
 * @link https://github.com/google/libaddressinput/wiki/AddressValidationMetadata
 * @license https://creativecommons.org/licenses/by/4.0/ CC BY 4.0
 */

return [
    'postalCodeLocality' => [
        '{{ streetAddress }}',
        '{{ postalCode }} {{ addressLocality }}',
        '{{ addressCountry }}',
    ],
    'postalCodeLocalityRegion' => [
        '{{ streetAddress }}',
        '{{ postalCode }} {{ addressLocality }} {{ addressRegion }}',
        '{{ addressCountry }}',
    ],
    'localityPostalCode' => [
        '{{ streetAddress }}',
        '{{ addressLocality }} {{ postalCode }}',
        '{{ addressCountry }}',
    ],
    'localityPostalCodeMultiline' => [
        '{{ streetAddress }}',
        '{{ addressLocality }}',
        '{{ postalCode }}',
        '{{ addressCountry }}',
    ],
    'localityRegionPostalCode' => [
        '{{ streetAddress }}',
        '{{ addressLocality }} {{ addressRegion }} {{ postalCode }}',
        '{{ addressCountry }}',
    ],
    'localityRegionPostalCodeMultiline' => [
        '{{ streetAddress }}',
        '{{ addressLocality }}',
        '{{ addressRegion }}',
        '{{ postalCode }}',
        '{{ addressCountry }}',
    ],
];
