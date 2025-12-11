<?php

namespace Hks\Schema\Transformer;

use Spatie\OpeningHours\OpeningHours;

/**
 * @extends Transformer<OpeningHours>
 */
class OpeningHoursSchemaTransformer extends Transformer
{
    public function transform(object $openingHours): array
    {
        return $openingHours->asStructuredData(timezone: $this->option('timezone'));
    }
}
