<?php

namespace Hks\Schema\Data\Types;

use Hks\Schema\TestCase;

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
}
