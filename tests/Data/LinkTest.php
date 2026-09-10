<?php

namespace Hks\Schema\Data;

use Hks\Schema\Concerns\InteractsWithTmp;
use Hks\Schema\Data\Types\LinkTarget;
use Hks\Schema\TestCase;
use Kirby\Cms\App;
use Kirby\Cms\File;
use Kirby\Cms\Page;
use Kirby\Cms\User;
use TypeError;

class LinkTest extends TestCase
{
    use InteractsWithTmp;

    public const TMP = __DIR__ . '/tmp/LinkTest';

    public function testFromArrayResolvesAnExternalUrlAsIs(): void
    {
        $link = Link::fromArray(['url' => 'https://example.com', 'title' => 'Example']);

        $this->assertSame('https://example.com', $link->url());
        $this->assertSame('Example', $link->title());
        $this->assertNull($link->model());
    }

    public function testFromArrayResolvesAPageReferenceToItsOwnUrl(): void
    {
        $this->app = new App([
            'roots' => ['index' => static::TMP],
            'site' => ['children' => [
                ['slug' => 'about', 'content' => ['uuid' => 'test-about']],
            ]],
        ]);

        $link = Link::fromArray(['url' => 'page://test-about']);

        $this->assertInstanceOf(Page::class, $link->page());
        $this->assertNull($link->file());
        $this->assertNull($link->user());
        $this->assertSame('/about', $link->url());
    }

    public function testModelResolvesAFileReference(): void
    {
        $this->app = new App([
            'roots' => ['index' => static::TMP],
            'site' => ['children' => [
                [
                    'slug' => 'about',
                    'content' => ['uuid' => 'test-about-for-file'],
                    'files' => [
                        ['filename' => 'portrait.jpg', 'content' => ['uuid' => 'test-portrait']],
                    ],
                ],
            ]],
        ]);

        $link = Link::fromArray(['url' => 'file://test-portrait']);

        $this->assertInstanceOf(File::class, $link->file());
        $this->assertNull($link->page());
        $this->assertInstanceOf(File::class, $link->model());
    }

    public function testUserReferenceHasNoNavigableUrl(): void
    {
        $this->app = new App([
            'roots' => ['index' => static::TMP],
            'users' => [
                ['id' => 'test-editor', 'email' => 'editor@example.com'],
            ],
        ]);

        $link = Link::fromArray(['url' => 'user://test-editor']);

        $this->assertInstanceOf(User::class, $link->user());
        $this->assertInstanceOf(User::class, $link->model());
        $this->assertNull($link->url());
    }

    public function testUrlIsNullForADanglingPageReferenceRatherThanTheRawScheme(): void
    {
        $link = Link::fromArray(['url' => 'page://does-not-exist']);

        $this->assertNull($link->model());
        $this->assertNull($link->url());
    }

    public function testToStructuredDataOmitsUrlForADanglingReference(): void
    {
        $link = Link::fromArray(['url' => 'page://does-not-exist', 'title' => 'Ghost']);

        $this->assertArrayNotHasKey('url', $link->toStructuredData());
    }

    public function testUrlIsResolvedFreshOnEveryCallRatherThanCachedAtConstruction(): void
    {
        // The model isn't resolved eagerly in `fromArray()`/the constructor — `model()`/`url()`
        // look it up from the raw `uri` on every call, so a page created only *after* the
        // `Link` itself still resolves correctly.
        $link = Link::fromArray(['url' => 'page://test-lazy-about']);

        $this->assertNull($link->page());

        $this->app = new App([
            'roots' => ['index' => static::TMP],
            'site' => ['children' => [
                ['slug' => 'about', 'content' => ['uuid' => 'test-lazy-about']],
            ]],
        ]);

        $this->assertInstanceOf(Page::class, $link->page());
        $this->assertSame('/about', $link->url());
    }

    public function testFromArrayResolvesAValidTargetOption(): void
    {
        $this->assertSame(LinkTarget::Blank, Link::fromArray(['url' => '#', 'target' => '_blank'])->target());
        $this->assertSame(LinkTarget::Top, Link::fromArray(['url' => '#', 'target' => '_top'])->target());
    }

    public function testFromArrayDefaultsTargetToSelfWhenMissing(): void
    {
        $this->assertSame(LinkTarget::Self, Link::fromArray(['url' => '#'])->target());
    }

    public function testFromArrayThrowsGivenAnInvalidTargetValue(): void
    {
        $this->expectException(TypeError::class);

        Link::fromArray(['url' => '#', 'target' => 'not-a-target']);
    }

    public function testToArrayIncludesTheRenderedFields(): void
    {
        $link = Link::fromArray([
            'url' => 'https://example.com',
            'title' => 'Example',
            'target' => '_blank',
            'rel' => 'bookmark',
        ]);

        $this->assertSame([
            'url' => 'https://example.com',
            'title' => 'Example',
            'target' => '_blank',
            'rel' => 'bookmark',
        ], $link->toArray());
    }

    public function testToStructuredDataRendersAWebPageNode(): void
    {
        $link = new Link(uri: 'https://example.com', title: 'Example');

        $this->assertSame([
            '@type' => 'WebPage',
            'url' => 'https://example.com',
            'name' => 'Example',
        ], $link->toStructuredData());
    }

    public function testIsActiveAndIsOpenAreFalseWithoutAResolvedPage(): void
    {
        $link = Link::fromArray(['url' => 'https://example.com']);

        $this->assertFalse($link->isActive());
        $this->assertFalse($link->isOpen());
    }
}
