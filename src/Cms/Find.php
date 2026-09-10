<?php

namespace Hks\Schema\Cms;

use Kirby\Cms\App;
use Kirby\Cms\File;
use Kirby\Cms\Files;
use Kirby\Cms\ModelWithContent;

class Find
{
    /**
     * Resolve a single file from a raw `files`-field id.
     */
    public static function file(?string $id, ?ModelWithContent $parent = null): ?File
    {
        if ($id === null || $id === '') {
            return null;
        }

        return App::instance()->file($id, $parent);
    }

    /**
     * Resolve a collection of files from a raw `files`-field value.
     */
    public static function files(mixed $value, ?ModelWithContent $parent = null): Files
    {
        $files = new Files([], $parent);

        foreach ((array) $value as $id) {
            if ($file = static::file($id, $parent)) {
                $files->add($file);
            }
        }

        return $files;
    }
}
