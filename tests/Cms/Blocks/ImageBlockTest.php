<?php

namespace Hks\Schema\Cms\Blocks;

use Hks\Schema\Data\Types\Fraction;
use Hks\Schema\TestCase;

class ImageBlockTest extends TestCase
{
    public function testRatioResolvesToAFractionThroughToDataType(): void
    {
        $block = new ImageBlock(['type' => 'image', 'content' => ['ratio' => '16/9']]);

        $this->assertTrue(
            $block->ratio()->toDataType('fraction')->isEqualTo(new Fraction(16, 9))
        );
    }

    public function testRatioResolvesToNullThroughToDataTypeWhenUnset(): void
    {
        $block = new ImageBlock(['type' => 'image', 'content' => []]);

        $this->assertNull($block->ratio()->toDataType('fraction'));
    }
}
