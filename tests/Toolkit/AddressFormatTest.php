<?php

namespace Hks\Schema\Toolkit;

use Hks\Schema\Data\Types\Country;
use Hks\Schema\Data\Types\PostalAddress;
use Hks\Schema\TestCase;
use Kirby\Cms\App;

class AddressFormatTest extends TestCase
{
    public const array POSTAL_CODE_LOCALITY = [
        '{{ streetAddress }}',
        '{{ postalCode }} {{ addressLocality }}',
        '{{ addressCountry }}',
    ];

    public function testForResolvesTheAddressSchemeOfACountry(): void
    {
        $format = AddressFormat::for(Country::from('de'));

        $this->assertSame('DE', $format->country()->code());
        $this->assertSame(static::POSTAL_CODE_LOCALITY, $format->lines());
    }

    public function testForReturnsNullForACountryWithoutFormat(): void
    {
        $this->assertNull(AddressFormat::for(Country::from('XX')));
    }

    public function testTheOptionOverridesAndAddsCountries(): void
    {
        $this->app = new App([
            'roots' => ['index' => '/dev/null'],
            'options' => [
                'hksagentur.schema.addressFormats' => [
                    'DE' => ['{{ addressLocality }}'],
                    'JP' => 'localityRegionPostalCode',
                ],
            ],
        ]);

        $this->assertSame(['{{ addressLocality }}'], AddressFormat::for(Country::from('DE'))->lines());
        $this->assertSame([
            '{{ streetAddress }}',
            '{{ addressLocality }} {{ addressRegion }} {{ postalCode }}',
            '{{ addressCountry }}',
        ], AddressFormat::for(Country::from('JP'))->lines());
    }

    public function testAnUnknownSchemeNameIsSkipped(): void
    {
        $this->app = new App([
            'roots' => ['index' => '/dev/null'],
            'options' => ['hksagentur.schema.addressFormats' => ['JP' => 'missing']],
        ]);

        $this->assertNull(AddressFormat::for(Country::from('JP')));
    }

    public function testAKnownCountryKeepsItsFormatRegardlessOfTheDefaultCountry(): void
    {
        $this->app = new App([
            'roots' => ['index' => '/dev/null'],
            'options' => ['hksagentur.schema.defaultCountry' => 'US'],
        ]);

        $france = PostalAddress::from(['addressLocality' => 'Paris', 'postalCode' => '75001', 'addressCountry' => 'FR']);
        $unknown = PostalAddress::from(['addressLocality' => 'Springfield', 'postalCode' => '12345', 'addressRegion' => 'XY', 'addressCountry' => 'XX']);

        $this->assertSame(['75001 Paris', 'Frankreich'], $france->addressLines(fn (string $value) => $value, 'de')->values());
        $this->assertSame(['Springfield, XY 12345', 'XX'], $unknown->addressLines(fn (string $value) => $value, 'de')->values());
    }
}
