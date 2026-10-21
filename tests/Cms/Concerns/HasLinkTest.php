<?php

namespace Hks\Schema\Cms\Concerns;

use Hks\Schema\Concerns\InteractsWithTmp;
use Hks\Schema\Data\Link;
use Hks\Schema\TestCase;
use Kirby\Cms\App;
use Kirby\Cms\Block;
use Kirby\Cms\Page;

class HasLinkTest extends TestCase
{
    use InteractsWithTmp;

    public const TMP = __DIR__ . '/tmp/HasLinkTest';

    public function testLinkResolvesToALinkDataObject(): void
    {
        $block = new HasLinkBlock([
            'type' => 'test',
            'content' => ['link' => ['url' => 'https://example.com', 'title' => 'Example', 'icon' => 'arrow.svg']],
        ]);

        $this->assertTrue($block->hasLink());
        $this->assertInstanceOf(Link::class, $block->link());
        $this->assertSame('https://example.com', $block->link()->url());
        $this->assertSame('arrow.svg', $block->link()->icon());
        $this->assertNull($block->target());
    }

    public function testLinkIsNullWhenTheFieldIsEmpty(): void
    {
        $block = new HasLinkBlock(['type' => 'test', 'content' => []]);

        $this->assertFalse($block->hasLink());
        $this->assertNull($block->link());
        $this->assertFalse($block->hasTarget());
    }

    public function testTargetResolvesThePageALinkPointsTo(): void
    {
        $this->app = new App([
            'roots' => ['index' => static::TMP],
            'site' => ['children' => [
                ['slug' => 'about', 'content' => ['uuid' => 'test-about']],
            ]],
        ]);

        $block = new HasLinkBlock([
            'type' => 'test',
            'content' => ['link' => ['url' => 'page://test-about']],
        ]);

        $this->assertTrue($block->hasTarget());
        $this->assertInstanceOf(Page::class, $block->target());
        $this->assertSame('about', $block->target()->slug());
    }
}

class HasLinkBlock extends Block
{
    use HasLink;
}
