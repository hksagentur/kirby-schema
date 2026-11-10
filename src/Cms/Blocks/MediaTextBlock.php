<?php

namespace Hks\Schema\Cms\Blocks;

use Hks\Schema\Cms\Concerns\HasLink;
use Hks\Schema\Cms\Concerns\HasMediaFile;
use Kirby\Cms\Block;
use Kirby\Content\Field;

/**
 * @method Field level()
 * @method Field title()
 * @method Field eyebrow()
 * @method Field text()
 * @method Field alignment()
 * @method Field reverse()
 */
class MediaTextBlock extends Block
{
    use HasLink;
    use HasMediaFile;
}
