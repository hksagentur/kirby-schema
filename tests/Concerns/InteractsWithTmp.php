<?php

namespace Hks\Schema\Concerns;

use Kirby\Cms\App;
use Kirby\Filesystem\Dir;

trait InteractsWithTmp
{
    protected function setUpInteractsWithTmp(): void
    {
        $this->app = new App(['roots' => ['index' => static::TMP]]);
    }

    protected function tearDownInteractsWithTmp(): void
    {
        Dir::remove(static::TMP);

        $parent = dirname(static::TMP);

        if (Dir::isEmpty($parent)) {
            Dir::remove($parent);
        }
    }
}
