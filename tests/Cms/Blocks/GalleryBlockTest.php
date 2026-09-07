<?php

namespace Hks\Schema\Cms\Blocks;

use Hks\Schema\Data\Types\Fraction;
use Hks\Schema\TestCase;

class GalleryBlockTest extends TestCase
{
    public function testRatioResolvesToAFractionThroughToDataType(): void
    {
        $block = new GalleryBlock(['type' => 'gallery', 'content' => ['ratio' => '4/3']]);

        $this->assertTrue(
            $block->ratio()->toDataType('fraction')->isEqualTo(new Fraction(4, 3))
        );
    }

    public function testRatioResolvesToNullThroughToDataTypeWhenUnset(): void
    {
        $block = new GalleryBlock(['type' => 'gallery', 'content' => []]);

        $this->assertNull($block->ratio()->toDataType('fraction'));
    }
}
