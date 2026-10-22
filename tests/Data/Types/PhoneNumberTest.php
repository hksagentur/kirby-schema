<?php

namespace Hks\Schema\Data\Types;

use Hks\Schema\TestCase;
use Kirby\Cms\App;

class PhoneNumberTest extends TestCase
{
    public function testANationalNumberWithoutAPlusPrefixIsNotMisreadAsInternational(): void
    {
        $phoneNumber = new PhoneNumber('0221 123456');

        $this->assertTrue($phoneNumber->isNational());
        $this->assertFalse($phoneNumber->isInternational());
        $this->assertNull($phoneNumber->countryCode());
        $this->assertSame('0221', $phoneNumber->nationalTrunkCode());
    }

    public function testAnInternationalNumberWithAPlusPrefixIsRecognizedAsSuch(): void
    {
        $phoneNumber = new PhoneNumber('+49 221 123456');

        $this->assertTrue($phoneNumber->isInternational());
        $this->assertFalse($phoneNumber->isNational());
        $this->assertSame('49', $phoneNumber->countryCode());
    }

    public function testHasNationalTrunkCodeIsFalseInsteadOfMatchingAnEmptyString(): void
    {
        $phoneNumber = new PhoneNumber('+49 30 1234567');

        $this->assertFalse($phoneNumber->hasNationalTrunkCode());
        $this->assertNull($phoneNumber->nationalTrunkCode());
    }

    public function testANationalNumberIsConvertedToE164UsingTheDefaultCountry(): void
    {
        $phoneNumber = new PhoneNumber('04202-91016-0');

        $this->assertSame('+494202910160', $phoneNumber->toE164String());
    }

    public function testANationalNumberIsConvertedToE164UsingTheGivenCountry(): void
    {
        $this->assertSame('+43123456', (new PhoneNumber('01 23456'))->toE164String('AT'));
        $this->assertSame('+390612345678', (new PhoneNumber('06 12345678'))->toE164String('IT'));
        $this->assertSame('+3611234567', (new PhoneNumber('06 1 1234567'))->toE164String('HU'));
    }

    public function testANationalNumberOfAnUnknownCountryKeepsItsDigits(): void
    {
        $this->assertSame('0123456', (new PhoneNumber('012 3456'))->toE164String('XX'));
    }

    public function testAnInternationalNumberDropsItsParenthesizedTrunkCode(): void
    {
        $this->assertSame('+49221123456', (new PhoneNumber('+49 (0) 221 123456'))->toE164String());
        $this->assertSame('+49221123456', (new PhoneNumber('+49 221 123456'))->toE164String());
    }

    public function testToHtmlLinksToTheE164Number(): void
    {
        $html = (new PhoneNumber('04202-91016-0'))->toHtml();

        $this->assertStringContainsString('href="tel:+494202910160"', $html);
        $this->assertStringContainsString('>04202-91016-0</a>', $html);
    }

    public function testAProjectCanAddACallingCodeWithoutReplacingTheShippedOnes(): void
    {
        $this->app = new App([
            'roots' => ['index' => '/dev/null'],
            'options' => [
                'hksagentur.schema.phoneNumberFormats' => [
                    'JP' => ['callingCode' => '81', 'trunkPrefix' => '0'],
                ],
            ],
        ]);

        $this->assertSame('+81312345678', (new PhoneNumber('03 1234 5678'))->toE164String('JP'));
        $this->assertSame('+494202910160', (new PhoneNumber('04202-91016-0'))->toE164String('DE'));
    }

    public function testAnInternationalNumberKeepsAParenthesizedAreaCode(): void
    {
        $this->assertSame('+49221123456', (new PhoneNumber('+49 (221) 123456'))->toE164String());
    }

    public function testAnInternationalNumberKeepsTheLeadingZeroOfACountryWithoutTrunkPrefix(): void
    {
        $this->assertSame('+390612345678', (new PhoneNumber('+39 06 12345678'))->toE164String());
    }

    public function testAnInternationalNumberDropsAParenthesizedTrunkPrefixOfMoreThanOneDigit(): void
    {
        $this->assertSame('+361234567', (new PhoneNumber('+36 (06) 1 234 567'))->toE164String());
    }

    public function testToE123StringDropsTheTrunkPrefixOfAnInternationalNumber(): void
    {
        $this->assertSame('+49 221 123456', (new PhoneNumber('+49 (0) 221 123456'))->toE123String());
        $this->assertSame('+49 221 123456', (new PhoneNumber('+49 (221) 123456'))->toE123String());
    }

    public function testToE123StringKeepsANationalNumberNational(): void
    {
        $this->assertSame('0221 123456', (new PhoneNumber('0221 123456'))->toE123String());
    }

    public function testToUriLinksToTheE164Number(): void
    {
        $this->assertSame('tel:+494202910160', (new PhoneNumber('04202-91016-0'))->toUri());
    }

    public function testTheInternationalPrefixZeroZeroIsReadLikeAPlus(): void
    {
        $number = new PhoneNumber('0049 4202 91016-0');

        $this->assertTrue($number->isInternational());
        $this->assertSame('49', $number->countryCode());
        $this->assertSame('+494202910160', $number->toE164String());
        $this->assertSame('+494202910160', (new PhoneNumber('0049 (0) 4202 910160'))->toE164String());
    }

    public function testTheCountryCodeEndsWhereTheCallingCodeEnds(): void
    {
        $this->assertSame('49', (new PhoneNumber('+494202910160'))->countryCode());
        $this->assertSame('1', (new PhoneNumber('+14155550100'))->countryCode());
        $this->assertSame('353', (new PhoneNumber('+3531234567'))->countryCode());
        $this->assertSame('49', (new PhoneNumber('00494202910160'))->countryCode());
    }

    public function testTheNationalSignificantNumberDropsTheTrunkPrefix(): void
    {
        $this->assertSame('04202 91016-0', (new PhoneNumber('04202 91016-0'))->nationalNumber());
        $this->assertSame('4202 91016-0', (new PhoneNumber('04202 91016-0'))->nationalSignificantNumber());
        $this->assertSame('4202 910160', (new PhoneNumber('+49 (0) 4202 910160'))->nationalSignificantNumber());
    }
}
