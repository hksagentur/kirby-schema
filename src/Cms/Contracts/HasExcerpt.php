<?php

namespace Hks\Schema\Cms\Contracts;

interface HasExcerpt
{
    public function excerpt(int $limit = 140, string $end = '…'): string;
}
