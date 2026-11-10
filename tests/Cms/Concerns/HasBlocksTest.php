<?php

namespace Hks\Schema\Cms\Concerns;

use Hks\Schema\TestCase;
use Kirby\Cms\App;
use Kirby\Cms\Page;

class HasBlocksTest extends TestCase
{
    public function testBlocksAreDecodedOnce(): void
    {
        $page = new HasBlocksPage([
            'slug' => 'test',
            'content' => ['blocks' => json_encode([
                ['type' => 'text', 'content' => ['text' => 'Hallo']],
            ])],
        ]);

        $this->assertCount(1, $page->blocks());
        $this->assertSame($page->blocks(), $page->blocks());
    }

    public function testBlocksAreCachedPerLanguage(): void
    {
        $page = $this->createMultilingualPage();

        $this->assertCount(1, $page->blocks('de'));
        $this->assertCount(2, $page->blocks('en'));
        $this->assertSame($page->blocks('de'), $page->blocks());
        $this->assertSame($page->blocks('en'), $page->blocks('en'));
    }

    public function testBlocksFollowTheCurrentLanguage(): void
    {
        $page = $this->createMultilingualPage();

        $this->assertCount(1, $page->blocks());
        $this->assertCount(1, $page->blocks('current'));

        $this->app->setCurrentLanguage('en');

        $this->assertCount(2, $page->blocks());
        $this->assertCount(2, $page->blocks('current'));
        $this->assertCount(1, $page->blocks('default'));
        $this->assertSame($page->blocks('de'), $page->blocks('default'));
    }

    public function testBlocksShareOneEntryOnSingleLanguageSites(): void
    {
        $page = new HasBlocksPage([
            'slug' => 'test',
            'content' => ['blocks' => json_encode([
                ['type' => 'text', 'content' => ['text' => 'Hallo']],
            ])],
        ]);

        $this->assertSame($page->blocks(), $page->blocks('de'));
    }

    protected function createMultilingualPage(): HasBlocksPage
    {
        $this->app = new App([
            'roots' => ['index' => '/dev/null'],
            'languages' => [
                ['code' => 'de', 'default' => true],
                ['code' => 'en'],
            ],
        ]);

        return new HasBlocksPage([
            'slug' => 'test',
            'translations' => [
                ['code' => 'de', 'content' => ['blocks' => json_encode([
                    ['type' => 'text', 'content' => ['text' => 'Hallo']],
                ])]],
                ['code' => 'en', 'content' => ['blocks' => json_encode([
                    ['type' => 'text', 'content' => ['text' => 'Hello']],
                    ['type' => 'text', 'content' => ['text' => 'World']],
                ])]],
            ],
        ]);
    }
}

class HasBlocksPage extends Page
{
    use HasBlocks;
}
