<?php

namespace Hks\Schema\Toolkit;

use Hks\Schema\TestCase;

class I18nTest extends TestCase
{
    public function testOxfordListReturnsASingleValueUnchanged(): void
    {
        $this->assertSame('Apple', I18n::oxfordList(['Apple'], locale: 'en'));
    }

    public function testOxfordListJoinsTwoValuesWithAnd(): void
    {
        $this->assertSame('Apple and Banana', I18n::oxfordList(['Apple', 'Banana'], locale: 'en'));
    }

    public function testOxfordListJoinsMoreThanTwoValuesWithAnOxfordComma(): void
    {
        $this->assertSame('Apple, Banana, and Cherry', I18n::oxfordList(['Apple', 'Banana', 'Cherry'], locale: 'en'));
    }

    public function testOxfordListTruncatesToASingleOtherWithSingularWording(): void
    {
        // Three values, limit 2 -> exactly one truncated item.
        $result = I18n::oxfordList(['Apple', 'Banana', 'Cherry'], limit: 2, locale: 'en');

        $this->assertSame('Apple, Banana, and 1 other', $result);
    }

    public function testOxfordListTruncatesToSeveralOthersWithPluralWording(): void
    {
        $this->assertSame(
            'Apple, Banana, and 2 others',
            I18n::oxfordList(['Apple', 'Banana', 'Cherry', 'Date'], limit: 2, locale: 'en'),
        );
        $this->assertSame(
            'Apple, Banana und 2 weitere',
            I18n::oxfordList(['Apple', 'Banana', 'Cherry', 'Date'], limit: 2, locale: 'de'),
        );
    }

    public function testOxfordListTruncatesToASingleOtherWithSingularWordingInGerman(): void
    {
        $result = I18n::oxfordList(['Apple', 'Banana', 'Cherry'], limit: 2, locale: 'de');

        $this->assertSame('Apple, Banana und 1 weitere:r', $result);
    }
}
