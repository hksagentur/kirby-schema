<?php

namespace Hks\Schema\Format;

use Hks\Schema\Data\Types\PostalAddress;
use Hks\Schema\TestCase;
use Kirby\Cms\App;

class PostalAddressFormatTest extends TestCase
{
    public const array POSTAL_CODE_LOCALITY = [
        '{{ streetAddress }}',
        '{{ postalCode }} {{ addressLocality }}',
        '{{ addressCountry }}',
    ];

    public function testGetResolvesTheAddressSchemeOfACountry(): void
    {
        $format = PostalAddressFormat::get('de');

        $this->assertSame('DE', $format->countryCode());
        $this->assertSame(static::POSTAL_CODE_LOCALITY, $format->lines());
    }

    public function testGetReturnsNullForAnUnknownCountry(): void
    {
        $this->assertNull(PostalAddressFormat::get('XX'));
    }

    public function testGetWithoutACodeReturnsTheDefaultCountry(): void
    {
        $this->assertSame('DE', PostalAddressFormat::get()->countryCode());
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

        $this->assertSame(['{{ addressLocality }}'], PostalAddressFormat::get('DE')->lines());
        $this->assertSame([
            '{{ streetAddress }}',
            '{{ addressLocality }} {{ addressRegion }} {{ postalCode }}',
            '{{ addressCountry }}',
        ], PostalAddressFormat::get('JP')->lines());
    }

    public function testDefaultReadsTheDefaultCountryOption(): void
    {
        $this->app = new App([
            'roots' => ['index' => '/dev/null'],
            'options' => ['hksagentur.schema.defaultCountry' => 'us'],
        ]);

        $this->assertSame('US', PostalAddressFormat::default()->countryCode());
    }

    public function testAnUnknownTemplateNameIsSkipped(): void
    {
        $this->app = new App([
            'roots' => ['index' => '/dev/null'],
            'options' => ['hksagentur.schema.addressFormats' => ['JP' => 'missing']],
        ]);

        $this->assertNull(PostalAddressFormat::get('JP'));
    }

    public function testDefaultIsNullWhenTheDefaultCountryHasNoFormat(): void
    {
        $this->app = new App([
            'roots' => ['index' => '/dev/null'],
            'options' => ['hksagentur.schema.defaultCountry' => 'XX'],
        ]);

        $this->assertNull(PostalAddressFormat::default());
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
