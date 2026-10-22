<?php

namespace Hks\Schema\Data\Types;

use Hks\Schema\TestCase;

class EmailAddressTest extends TestCase
{
    public function testToUriLinksToTheAddress(): void
    {
        $this->assertSame('mailto:jane@example.com', (new EmailAddress('jane@example.com'))->toUri());
    }

    public function testToHtmlObfuscatesTheAddress(): void
    {
        $html = (new EmailAddress('jane@example.com'))->toHtml();

        $this->assertStringNotContainsString('jane@example.com', $html);
        $this->assertStringContainsString('href="mailto:', $html);
    }
}
