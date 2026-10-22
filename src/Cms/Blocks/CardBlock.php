<?php

namespace Hks\Schema\Cms\Blocks;

use Hks\Schema\Cms\Concerns\HasMediaFile;
use Hks\Schema\Cms\Concerns\HasLink;
use Kirby\Cms\Block;
use Kirby\Content\Field;

/**
 * @method Field level()
 * @method Field title()
 * @method Field eyebrow()
 * @method Field text()
 */
class CardBlock extends Block
{
    use HasLink;
    use HasMediaFile;
}
