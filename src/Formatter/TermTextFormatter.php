<?php

namespace Hks\Schema\Formatter;

use Hks\Schema\Data\Term;

/**
 * @extends Formatter<Term>
 */
class TermTextFormatter extends Formatter
{
    public function format(mixed $term): string
    {
        return $term->name()->toString();
    }
}
