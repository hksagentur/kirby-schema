<?php

namespace Hks\Schema\Formatter;

use Hks\Schema\Data\Organization;
use Kirby\Toolkit\A;

/**
 * @extends Formatter<Organization>
 */
class OrganizationHtmlFormatter extends Formatter
{
    public function defaults(): array
    {
        return [
            'format' => 'microformat',
        ];
    }

    public function format(mixed $organization): string
    {
        $options = A::without($this->options(), ['format']);

        $formatter = match ($this->option('format')) {
            'microdata' => new OrganizationMicrodataFormatter($options),
            default => new OrganizationMicroformatFormatter($options),
        };

        return $formatter->format($organization);
    }
}
