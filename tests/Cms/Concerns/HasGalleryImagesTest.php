<?php

namespace Hks\Schema\Cms\Concerns;

use Hks\Schema\TestCase;
use Kirby\Cms\App;
use Kirby\Cms\Page;

class HasGalleryImagesTest extends TestCase
{
    public function testGalleryImagesAreResolvedOnce(): void
    {
        $page = new HasGalleryImagesPage([
            'slug' => 'test',
            'files' => [['filename' => 'a.jpg'], ['filename' => 'b.jpg']],
            'content' => ['gallery' => "- a.jpg\n- b.jpg"],
        ]);

        $this->assertCount(2, $page->galleryImages());
        $this->assertSame($page->galleryImages(), $page->galleryImages());
        $this->assertTrue($page->hasGalleryImages());
    }

    public function testGalleryImagesAreCachedPerLanguage(): void
    {
        $page = $this->createMultilingualPage();

        $this->assertCount(1, $page->galleryImages('de'));
        $this->assertCount(2, $page->galleryImages('en'));
        $this->assertSame($page->galleryImages('de'), $page->galleryImages());
    }

    public function testGalleryImagesFollowTheCurrentLanguage(): void
    {
        $page = $this->createMultilingualPage();

        $this->assertCount(1, $page->galleryImages());

        $this->app->setCurrentLanguage('en');

        $this->assertCount(2, $page->galleryImages());
        $this->assertCount(1, $page->galleryImages('default'));
    }

    protected function createMultilingualPage(): HasGalleryImagesPage
    {
        $this->app = new App([
            'roots' => ['index' => '/dev/null'],
            'languages' => [
                ['code' => 'de', 'default' => true],
                ['code' => 'en'],
            ],
        ]);

        return new HasGalleryImagesPage([
            'slug' => 'test',
            'files' => [['filename' => 'a.jpg'], ['filename' => 'b.jpg']],
            'translations' => [
                ['code' => 'de', 'content' => ['gallery' => '- a.jpg']],
                ['code' => 'en', 'content' => ['gallery' => "- a.jpg\n- b.jpg"]],
            ],
        ]);
    }
}

class HasGalleryImagesPage extends Page
{
    use HasGalleryImages;
}
