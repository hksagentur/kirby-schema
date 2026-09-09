<?php

namespace Hks\Schema\Concerns;

trait AssertsHtml
{
    public function assertSameHtml(string $expected, string $actual, string $message = ''): void
    {
        $this->assertSame($this->minifyHtml($expected), $this->minifyHtml($actual), $message);
    }

    protected function minifyHtml(string $html): string
    {
        return trim(preg_replace('/[ \t]{2,}|\s*\n\s*/', '', $html));
    }
}
