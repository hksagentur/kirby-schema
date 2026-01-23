<?php

namespace Hks\Schema\Cms\Contracts;

use Kirby\Cms\File;

interface HasKeyVisual
{
    public function keyVisual(): ?File;
}
