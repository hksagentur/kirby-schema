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

    public function testOxfordListTruncationBranchDoesNotFatalOnMultipleRemainingItems(): void
    {
        // Regression test for a missing `use Kirby\Toolkit\Str;` import that
        // made this branch (the only one reaching `Str::template()`) fatal
        // with "Class not found" on every call — not asserting the exact
        // wording here since the ">1 remaining" plural form in lang/en.php is
        // itself a separate, pre-existing bug (flagged, not fixed here).
        $result = I18n::oxfordList(['Apple', 'Banana', 'Cherry', 'Date'], limit: 2, locale: 'en');

        $this->assertStringStartsWith('Apple, Banana', $result);
    }

    public function testOxfordListTruncatesToASingleOtherWithSingularWordingInGerman(): void
    {
        $result = I18n::oxfordList(['Apple', 'Banana', 'Cherry'], limit: 2, locale: 'de');

        $this->assertSame('Apple, Banana und 1 weitere:r', $result);
    }
}
