<?php

namespace Hks\Schema\Data\Types;

use Hks\Schema\Data\Concerns\TransformsStrings;

readonly class Markup extends StringType
{
    use TransformsStrings;
}
