<?php

namespace Hks\Schema\Formatter;

use Hks\Schema\Data\PostalAddress;

/**
 * @extends MicrodataFormatter<PostalAddress>
 */
class PostalAddressMicrodataFormatter extends MicrodataFormatter
{
    public function getSchemaType(): string
    {
        return 'https://schema.org/PostalAddress';
    }

    public function getAttributes(mixed $postalAddress): array
    {
        return [
            'streetAddress' => fn () => $postalAddress->streetAddress()?->toHtml(['itemprop' => 'streetAddress']),
            'postalCode' => fn () => $postalAddress->postalCode()?->toHtml(['itemprop' => 'postalCode']),
            'locality' => fn () => $postalAddress->locality()?->toHtml(['itemprop' => 'addressLocality']),
            'country' => fn () => $postalAddress->country()?->toHtml(['itemprop' => 'addressCountry']),
        ];
    }
}
