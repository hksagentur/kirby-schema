<?php

namespace Hks\Schema\Cms\Blocks;

use Kirby\Cms\Block;
use Kirby\Content\Field;

class QuoteBlock extends Block
{
    public function text(): Field
    {
        return $this->content()->text();
    }

    public function citation(): Field
    {
        return $this->content()->citation();
    }
}
