<?php

namespace Hks\Schema\Cms\Blocks;

use Kirby\Cms\Block;
use Kirby\Content\Field;

class MarkdownBlock extends Block
{
    public function text(): Field
    {
        return $this->content()->text();
    }
}
