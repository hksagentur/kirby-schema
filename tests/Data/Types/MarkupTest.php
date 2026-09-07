<?php

namespace Hks\Schema\Data\Types;

use Hks\Schema\TestCase;

class MarkupTest extends TestCase
{
    public function testToHtmlPassesTheTrustedMarkupThroughUnescaped(): void
    {
        $markup = Markup::from('<strong>Hi</strong>');

        $this->assertSame('<strong>Hi</strong>', $markup->toHtml());
    }

    public function testToPlainTextStripsTheMarkupTags(): void
    {
        $markup = Markup::from('<strong>Hi</strong>');

        $this->assertSame('Hi', $markup->toPlainText());
    }
}
