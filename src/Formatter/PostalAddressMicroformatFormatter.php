<?php

namespace Hks\Schema\Formatter;

use Hks\Schema\Data\PostalAddress;

/**
 * @extends MicroformatFormatter<PostalAddress>
 */
class PostalAddressMicroformatFormatter extends MicroformatFormatter
{
    public function getMicroformatType(): string
    {
        return 'h-adr';
    }

    public function getAttributes(mixed $postalAddress): array
    {
        return [
            'streetAddress' => fn () => $postalAddress->streetAddress()?->toHtml(['class' => 'p-street-address']),
            'postalCode' => fn () => $postalAddress->postalCode()?->toHtml(['class' => 'p-postal-code']),
            'locality' => fn () => $postalAddress->locality()?->toHtml(['class' => 'p-locality']),
            'country' => fn () => $postalAddress->country()?->toHtml(['class' => 'p-country-name']),
        ];
    }
}
