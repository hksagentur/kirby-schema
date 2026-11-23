<?php

namespace Hks\Schema\Toolkit;

use Hks\Schema\Data\Types\Country;
use Hks\Schema\TestCase;
use Kirby\Cms\App;

class CallingCodeTest extends TestCase
{
    public function testForReturnsTheCallingCodeAndNationalPrefixOfACountry(): void
    {
        $callingCode = CallingCode::for(Country::from('de'));

        $this->assertSame('49', $callingCode->code());
        $this->assertSame('0', $callingCode->nationalPrefix());
    }

    public function testACountryWithoutNationalPrefixHasNone(): void
    {
        $this->assertNull(CallingCode::for(Country::from('IT'))->nationalPrefix());
    }

    public function testForReturnsNullForACountryWithoutCallingCode(): void
    {
        $this->assertNull(CallingCode::for(Country::from('XX')));
    }

    public function testFindLooksUpACallingCode(): void
    {
        $this->assertSame('0', CallingCode::find('49')->nationalPrefix());
        $this->assertNull(CallingCode::find('999'));
    }

    public function testTheOptionOverridesSingleValuesAndAddsCountries(): void
    {
        $this->app = new App([
            'roots' => ['index' => '/dev/null'],
            'options' => [
                'hksagentur.schema.callingCodes' => [
                    'DE' => ['nationalPrefix' => '00'],
                    'JP' => ['callingCode' => '81', 'nationalPrefix' => '0'],
                    'XX' => ['nationalPrefix' => '0'],
                ],
            ],
        ]);

        $this->assertSame('49', CallingCode::for(Country::from('DE'))->code());
        $this->assertSame('00', CallingCode::for(Country::from('DE'))->nationalPrefix());
        $this->assertSame('81', CallingCode::for(Country::from('JP'))->code());
        $this->assertNull(CallingCode::for(Country::from('XX')));
    }

    public function testUnknownKeysOfTheOptionAreIgnored(): void
    {
        $this->app = new App([
            'roots' => ['index' => '/dev/null'],
            'options' => [
                'hksagentur.schema.callingCodes' => [
                    'JP' => ['callingCode' => '81', 'nationalprefix' => '0'],
                ],
            ],
        ]);

        $this->assertSame('81', CallingCode::for(Country::from('JP'))->code());
        $this->assertNull(CallingCode::for(Country::from('JP'))->nationalPrefix());
    }
}
