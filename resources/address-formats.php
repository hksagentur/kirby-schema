<?php

/**
 * Address formats adapted from Google's address metadata.
 *
 * @link https://github.com/google/libaddressinput/wiki/AddressValidationMetadata
 * @license https://creativecommons.org/licenses/by/4.0/ CC BY 4.0
 */

return [
    'AT' => 'postalCodeLocality',
    'AU' => 'localityRegionPostalCode',
    'BE' => 'postalCodeLocality',
    'BG' => 'postalCodeLocality',
    'CA' => 'localityRegionPostalCode',
    'CH' => 'postalCodeLocality',
    'CY' => 'postalCodeLocality',
    'CZ' => 'postalCodeLocality',
    'DE' => 'postalCodeLocality',
    'DK' => 'postalCodeLocality',
    'EE' => 'postalCodeLocalityRegion',
    'ES' => 'postalCodeLocalityRegion',
    'FI' => 'postalCodeLocality',
    'FR' => 'postalCodeLocality',
    'GB' => 'localityPostalCodeMultiline',
    'GR' => 'postalCodeLocality',
    'HR' => 'postalCodeLocality',
    'HU' => [
        '{{ addressLocality }}',
        '{{ streetAddress }}',
        '{{ postalCode }}',
        '{{ addressCountry }}',
    ],
    'IE' => 'localityRegionPostalCodeMultiline',
    'IS' => 'postalCodeLocality',
    'IT' => 'postalCodeLocalityRegion',
    'LI' => 'postalCodeLocality',
    'LT' => 'postalCodeLocalityRegion',
    'LU' => 'postalCodeLocality',
    'LV' => [
        '{{ streetAddress }}',
        '{{ addressRegion }}',
        '{{ addressLocality }}, {{ postalCode }}',
        '{{ addressCountry }}',
    ],
    'MT' => 'localityPostalCode',
    'NL' => 'postalCodeLocality',
    'NO' => 'postalCodeLocality',
    'NZ' => 'localityPostalCode',
    'PL' => 'postalCodeLocality',
    'PT' => 'postalCodeLocality',
    'RO' => [
        '{{ streetAddress }}',
        '{{ postalCode }} {{ addressRegion }} {{ addressLocality }}',
        '{{ addressCountry }}',
    ],
    'SE' => 'postalCodeLocality',
    'SI' => 'postalCodeLocality',
    'SK' => 'postalCodeLocality',
    'TR' => [
        '{{ streetAddress }}',
        '{{ postalCode }} {{ addressLocality }}/{{ addressRegion }}',
        '{{ addressCountry }}',
    ],
    'US' => [
        '{{ streetAddress }}',
        '{{ addressLocality }}, {{ addressRegion }} {{ postalCode }}',
        '{{ addressCountry }}',
    ],
];
