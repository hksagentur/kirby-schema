<?php

namespace Hks\Schema\Image;

use Hks\MediaKit\Cms\PendingImage;
use Hks\MediaKit\Html\Attributes;
use Kirby\Cms\App;
use Kirby\Toolkit\A;

/**
 * @deprecated Use \Hks\MediaKit\Cms\PendingImage instead. This class is kept for backwards
 *             compatibility and will be removed in a future major. It merges the legacy
 *             `hksagentur.schema.image.*` options into `hksagentur.media-kit.image.*`.
 */
class ResponsiveImage extends PendingImage
{
    protected function optionsFromConfig(): static
    {
        $options = A::merge(
            App::instance()->option('hksagentur.schema.image', []),
            App::instance()->option('hksagentur.media-kit.image', []),
            A::MERGE_REPLACE
        );

        $this->formats = $options['formats'] ?? ['jpeg'];
        $this->widths = $options['widths'] ?? ['auto'];

        $this->attributes = Attributes::from($options['attributes'] ?? []);

        return $this;
    }
}
