<?php

namespace Hks\Schema\Data\Types;

use Hks\Schema\TestCase;

class UrlTest extends TestCase
{
    public function testWithPathKeepsTheRestOfTheUrlAndReplacesThePath(): void
    {
        $url = Url::from('https://example.com')->withPath('/a');

        $this->assertSame('https://example.com/a', $url->value());
    }

    public function testWithFragmentKeepsTheRestOfTheUrl(): void
    {
        $url = Url::from('https://example.com/a')->withFragment('top');

        $this->assertSame('https://example.com/a#top', $url->value());
    }

    public function testWithQueryKeepsTheRestOfTheUrl(): void
    {
        $url = Url::from('https://example.com/a')->withQuery(['page' => 2]);

        $this->assertSame('https://example.com/a?page=2', $url->value());
    }

    public function testPathReturnsNullWhenTheUrlHasNoPath(): void
    {
        $url = Url::from('https://example.com');

        $this->assertNull($url->path());
    }
}
