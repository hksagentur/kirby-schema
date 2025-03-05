<?php

namespace Hks\Schema\Cms;

trait HasTags
{
    public function isTaggedWith(string ...$needles): bool
    {
        $tags = $this->content()->tags()->split();

        foreach ($needles as $needle) {
            if (! in_array($needle, $tags)) {
                return false;
            }
        }

        return true;
    }
}
