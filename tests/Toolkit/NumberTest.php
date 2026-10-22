<?php

namespace Hks\Schema\Toolkit;

use Hks\Schema\TestCase;

class NumberTest extends TestCase
{
    public function testFormatUsesTheLocale(): void
    {
        $this->assertSame('1.234,5', Number::format(1234.5, 'de'));
        $this->assertSame('1,234.5', Number::format(1234.5, 'en'));
    }

    public function testFormatAppliesPrecisionAndRounding(): void
    {
        $this->assertSame('1.234,50', Number::format(1234.5, 'de', ['precision' => 2]));
        $this->assertSame('1.240', Number::format(1234.5, 'de', ['roundingIncrement' => 10, 'roundingMode' => 'ceiling']));
    }

    public function testRangeRoundsItsBoundsOutwards(): void
    {
        $options = ['maxPrecision' => 0];

        $this->assertSame('120–181', Number::range(120.4, 180.2, 'de', $options));
        $this->assertSame('ab 120', Number::range(120.9, null, 'de', $options));
        $this->assertSame('bis 181', Number::range(null, 180.2, 'de', $options));
        $this->assertSame('120', Number::range(120.0, 120.0, 'de', $options));
        $this->assertSame('', Number::range(null, null, 'de'));
    }
}
