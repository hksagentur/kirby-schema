<?php

namespace Hks\Schema\Video;

use Hks\MediaKit\Cms\PendingVideo;
use Hks\MediaKit\Html\Attributes;
use Kirby\Cms\App;
use Kirby\Toolkit\A;

/**
 * @deprecated Use \Hks\MediaKit\Cms\PendingVideo instead. This class is kept for backwards
 *             compatibility and will be removed in a future major. It merges the legacy
 *             `hksagentur.schema.video.*` options into `hksagentur.media-kit.video.*`.
 */
class ResponsiveVideo extends PendingVideo
{
    protected function optionsFromConfig(): static
    {
        $options = A::merge(
            App::instance()->option('hksagentur.schema.video', []),
            App::instance()->option('hksagentur.media-kit.video', []),
            A::MERGE_REPLACE
        );

        $this->attributes = Attributes::from($options['attributes'] ?? []);

        return $this;
    }
}
