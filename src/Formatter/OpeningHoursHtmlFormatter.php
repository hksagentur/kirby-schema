<?php

namespace Hks\Schema\Formatter;

use Spatie\OpeningHours\OpeningHours;

/**
 * @extends Formatter<OpeningHours>
 */
class OpeningHoursHtmlFormatter extends Formatter
{
    public function format(mixed $openingHours): string
    {
        return (new OpeningHoursMicrodataFormatter(
            $this->options(),
        ))->format($openingHours);
    }
}
