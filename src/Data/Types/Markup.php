<?php

namespace Hks\Schema\Data\Types;

use Hks\Schema\Data\Concerns\TransformsStrings;
use Hks\Schema\Data\Contracts\Htmlable;

readonly class Markup extends StringType implements Htmlable
{
    use TransformsStrings;
}
