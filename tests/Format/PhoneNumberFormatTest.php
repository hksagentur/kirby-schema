<?php

namespace Hks\Schema\Format;

use Hks\Schema\TestCase;
use Kirby\Cms\App;

class PhoneNumberFormatTest extends TestCase
{
    public function testGetReturnsTheCallingCodeAndTrunkPrefixOfACountry(): void
    {
        $format = PhoneNumberFormat::get('de');

        $this->assertSame('DE', $format->countryCode());
        $this->assertSame('49', $format->callingCode());
        $this->assertSame('0', $format->trunkPrefix());
    }

    public function testACountryWithoutTrunkPrefixHasNone(): void
    {
        $this->assertNull(PhoneNumberFormat::get('IT')->trunkPrefix());
    }

    public function testGetReturnsNullForAnUnknownCountry(): void
    {
        $this->assertNull(PhoneNumberFormat::get('XX'));
    }

    public function testGetWithoutACodeReturnsTheDefaultCountry(): void
    {
        $this->assertSame('DE', PhoneNumberFormat::get()->countryCode());
    }

    public function testByCallingCodeFindsTheCountry(): void
    {
        $this->assertSame('DE', PhoneNumberFormat::byCallingCode('49')->countryCode());
        $this->assertNull(PhoneNumberFormat::byCallingCode('999'));
    }

    public function testTheOptionOverridesSingleValuesAndAddsCountries(): void
    {
        $this->app = new App([
            'roots' => ['index' => '/dev/null'],
            'options' => [
                'hksagentur.schema.phoneNumberFormats' => [
                    'DE' => ['trunkPrefix' => '00'],
                    'JP' => ['callingCode' => '81', 'trunkPrefix' => '0'],
                    'XX' => ['trunkPrefix' => '0'],
                ],
            ],
        ]);

        $this->assertSame('49', PhoneNumberFormat::get('DE')->callingCode());
        $this->assertSame('00', PhoneNumberFormat::get('DE')->trunkPrefix());
        $this->assertSame('81', PhoneNumberFormat::get('JP')->callingCode());
        $this->assertNull(PhoneNumberFormat::get('XX'));
    }

    public function testDefaultIsNullWhenTheDefaultCountryHasNoCallingCode(): void
    {
        $this->app = new App([
            'roots' => ['index' => '/dev/null'],
            'options' => ['hksagentur.schema.defaultCountry' => 'XX'],
        ]);

        $this->assertNull(PhoneNumberFormat::default());
    }

    public function testFactoryBuildsAFormatFromProps(): void
    {
        $format = PhoneNumberFormat::factory(['countryCode' => 'JP', 'callingCode' => '81', 'trunkPrefix' => '0']);

        $this->assertSame('81', $format->callingCode());
        $this->assertSame('0', $format->trunkPrefix());
    }

    public function testUnknownKeysOfTheOptionAreIgnored(): void
    {
        $this->app = new App([
            'roots' => ['index' => '/dev/null'],
            'options' => [
                'hksagentur.schema.phoneNumberFormats' => [
                    'JP' => ['callingCode' => '81', 'trunkprefix' => '0'],
                ],
            ],
        ]);

        $this->assertSame('81', PhoneNumberFormat::get('JP')->callingCode());
        $this->assertNull(PhoneNumberFormat::get('JP')->trunkPrefix());
    }
}
