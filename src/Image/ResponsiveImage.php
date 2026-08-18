<?php

namespace Hks\Schema\Image;

use Hks\MediaKit\ResponsiveImage as BaseResponsiveImage;
use Kirby\Cms\App;
use Kirby\Toolkit\A;

/**
 * @deprecated Use \Hks\MediaKit\ResponsiveImage instead. This class is kept for backwards
 *             compatibility and will be removed in v2.0. It merges the legacy
 *             `hksagentur.schema.image.*` options into `hksagentur.media-kit.image.*`.
 */
class ResponsiveImage extends BaseResponsiveImage
{
    protected function getPluginOptions(?string $key = null, mixed $default = null): mixed
    {
        $options = [
            'image' => A::merge(
                App::instance()->option('hksagentur.schema.image', []),
                App::instance()->option('hksagentur.media-kit.image', []),
                A::MERGE_REPLACE
            ),
        ];

        if (is_null($key)) {
            return $options;
        }

        return A::get($options, $key, $default);
    }
}
