<?php

namespace Hks\Schema\Cms\Blocks;

use Hks\Schema\Data\Types\Fraction;
use Hks\Schema\TestCase;

class VideoBlockTest extends TestCase
{
    public function testRatioResolvesToAFractionThroughToDataType(): void
    {
        $block = new VideoBlock(['type' => 'video', 'content' => ['ratio' => '16/9']]);

        $this->assertTrue(
            $block->ratio()->toDataType('fraction')->isEqualTo(new Fraction(16, 9))
        );
    }

    public function testRatioResolvesToNullThroughToDataTypeWhenUnset(): void
    {
        $block = new VideoBlock(['type' => 'video', 'content' => []]);

        $this->assertNull($block->ratio()->toDataType('fraction'));
    }
}
