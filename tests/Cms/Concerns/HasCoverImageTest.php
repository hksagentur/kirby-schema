<?php

namespace Hks\Schema\Cms\Concerns;

use Hks\Schema\TestCase;
use Kirby\Cms\App;
use Kirby\Cms\Page;

class HasCoverImageTest extends TestCase
{
    public function testCoverImageIsResolvedOnce(): void
    {
        $page = new HasCoverImagePage([
            'slug' => 'test',
            'files' => [['filename' => 'a.jpg']],
            'content' => ['cover' => 'a.jpg'],
        ]);

        $this->assertSame('a.jpg', $page->coverImage()?->filename());
        $this->assertSame($page->coverImage(), $page->coverImage());
        $this->assertTrue($page->hasCoverImage());
    }

    public function testMissingCoverImageIsNull(): void
    {
        $page = new HasCoverImagePage(['slug' => 'test']);

        $this->assertNull($page->coverImage());
        $this->assertFalse($page->hasCoverImage());
    }

    public function testCoverImageIsCachedPerLanguage(): void
    {
        $page = $this->createMultilingualPage();

        $this->assertSame('a.jpg', $page->coverImage('de')?->filename());
        $this->assertSame('b.jpg', $page->coverImage('en')?->filename());
        $this->assertSame($page->coverImage('de'), $page->coverImage());
    }

    public function testCoverImageFollowsTheCurrentLanguage(): void
    {
        $page = $this->createMultilingualPage();

        $this->assertSame('a.jpg', $page->coverImage()?->filename());

        $this->app->setCurrentLanguage('en');

        $this->assertSame('b.jpg', $page->coverImage()?->filename());
        $this->assertSame('a.jpg', $page->coverImage('default')?->filename());
    }

    protected function createMultilingualPage(): HasCoverImagePage
    {
        $this->app = new App([
            'roots' => ['index' => '/dev/null'],
            'languages' => [
                ['code' => 'de', 'default' => true],
                ['code' => 'en'],
            ],
        ]);

        return new HasCoverImagePage([
            'slug' => 'test',
            'files' => [['filename' => 'a.jpg'], ['filename' => 'b.jpg']],
            'translations' => [
                ['code' => 'de', 'content' => ['cover' => 'a.jpg']],
                ['code' => 'en', 'content' => ['cover' => 'b.jpg']],
            ],
        ]);
    }
}

class HasCoverImagePage extends Page
{
    use HasCoverImage;
}
