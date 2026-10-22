<?php

namespace Hks\Schema\Cms\Blocks;

use Hks\Schema\Cms\Concerns\HasImage;
use Kirby\Cms\Block;
use Kirby\Content\Field;

/**
 * @method Field location()
 * @method Field src()
 * @method Field alt()
 * @method Field caption()
 * @method Field link()
 * @method Field ratio()
 * @method Field crop()
 * @method Field loading()
 * @method Field priority()
 */
class ImageBlock extends Block
{
    use HasImage;
}
