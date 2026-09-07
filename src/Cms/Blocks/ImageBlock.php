<?php

namespace Hks\Schema\Cms\Blocks;

use Kirby\Cms\Block;
use Kirby\Cms\File;
use Kirby\Content\Field;

/**
 * @method Field location()
 * @method Field link()
 * @method Field crop()
 * @method Field loading()
 * @method Field priority()
 * @method Field ratio()
 */
class ImageBlock extends Block
{
    protected ?File $image = null;

    public function src(): Field
    {
        return $this->content()->src()->or($this->image()?->url());
    }

    public function image(): ?File
    {
        return $this->image ??= $this->content()->image()->toFile();
    }

    public function alt(): Field
    {
        return $this->content()->alt()->or($this->image()?->alt());
    }

    public function caption(): Field
    {
        return $this->content()->caption()->or($this->image()?->caption());
    }
}
