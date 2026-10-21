<?php

namespace Hks\Schema\Toolkit;

use Hks\Schema\TestCase;

class StrTest extends TestCase
{
    public function testSquishCollapsesWhitespaceAndTrimsTheEnds(): void
    {
        $this->assertSame('Springfield, 12345', Str::squish("  Springfield,  \t12345\n"));
    }

    public function testSquishHandlesMultibyteWhitespace(): void
    {
        $this->assertSame('Bremer Straße 149', Str::squish("Bremer\u{00A0}\u{00A0}Straße 149"));
    }

    public function testSquishRemovesInvisibleCharactersAtTheEnds(): void
    {
        $this->assertSame('Achim', Str::squish("\u{FEFF}Achim\u{200B} "));
    }

    public function testSquishKeepsInvisibleCharactersInsideTheText(): void
    {
        $this->assertSame("Bau\u{00AD}gesellschaft", Str::squish("Bau\u{00AD}gesellschaft"));
    }
}
