<?php

namespace Hks\Schema\Data\Types;

use Hks\Schema\Data\Concerns\TransformsStrings;

readonly class Text extends StringType
{
    use TransformsStrings;
}
