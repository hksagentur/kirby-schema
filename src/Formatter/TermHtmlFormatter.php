<?php

namespace Hks\Schema\Formatter;

use Hks\Schema\Data\Term;

/**
 * @extends Formatter<Term>
 */
class TermHtmlFormatter extends Formatter
{
    public function format(mixed $term): string
    {
        return (new TermMicrodataFormatter(
            $this->options(),
        ))->format($term);
    }
}
