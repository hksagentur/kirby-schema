<?php

namespace Hks\Schema\Data\Types;

use Hks\Schema\Data\Concerns\TransformsStrings;

readonly class Markdown extends StringType
{
    use TransformsStrings;
}
