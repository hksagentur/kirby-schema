<?php

namespace Hks\Schema\Cms;

use Hks\Schema\TestCase;
use Kirby\Cms\Files;
use Kirby\Cms\Page;

class FindTest extends TestCase
{
    public function testFileReturnsNullWhenTheValueIsEmpty(): void
    {
        $this->assertNull(Find::file(null));
        $this->assertNull(Find::file([]));
    }

    public function testFileReturnsNullWhenTheUuidCannotBeResolved(): void
    {
        $this->assertNull(Find::file('file://does-not-exist'));
        $this->assertNull(Find::file(['file://does-not-exist']));
    }

    public function testFileAcceptsAnOptionalParentToPreferItsOwnFiles(): void
    {
        $page = new Page(['slug' => 'test']);

        $this->assertNull(Find::file('file://does-not-exist', $page));
    }

    public function testFilesReturnsAnEmptyCollectionWhenNoUuidResolves(): void
    {
        $files = Find::files(['file://does-not-exist', 'file://also-missing']);

        $this->assertInstanceOf(Files::class, $files);
        $this->assertCount(0, $files);
    }

}
