<?php

namespace Hks\Schema\Formatter;

use Hks\Schema\Data\PostalAddress;
use Kirby\Toolkit\A;

/**
 * @extends Formatter<PostalAddress>
 */
class PostalAddressHtmlFormatter extends Formatter
{
    public function defaults(): array
    {
        return [
            'format' => 'microformat',
        ];
    }

    public function format(mixed $postalAddress): string
    {
        $options = A::without($this->options(), ['format']);

        $formatter = match ($this->option('format')) {
            'microdata' => new PostalAddressMicrodataFormatter($options),
            default => new PostalAddressMicroformatFormatter($options),
        };

        return $formatter->format($postalAddress);
    }
}
