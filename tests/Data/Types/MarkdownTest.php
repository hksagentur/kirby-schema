<?php

namespace Hks\Schema\Data\Types;

use Hks\Schema\Data\Contracts\Htmlable;
use Hks\Schema\TestCase;

class MarkdownTest extends TestCase
{
    public function testImplementsHtmlable(): void
    {
        $this->assertInstanceOf(Htmlable::class, Markdown::from('Hi'));
    }
}
