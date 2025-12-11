<?php

namespace Hks\Schema\Formatter;

use Hks\Schema\Data\PostalAddress;
use Kirby\Toolkit\I18n;

/**
 * @extends Formatter<PostalAddress>
 */
class PostalAddressTextFormatter extends StructuredDataFormatter
{
    public function getAttributes(mixed $postalAddress): array
    {
        return [
            'streetAddress' => fn () => $postalAddress->streetAddress()?->toString(),
            'postalCode' => fn () => $postalAddress->postalCode()?->toString(),
            'locality' => fn () => $postalAddress->locality()?->toString(),
            'country' => fn () => $postalAddress->country()?->toString(),
        ];
    }

    public function compose(array $content): string
    {
        return I18n::template('hksagentur.schema.formatters.postalAddress', replace: $content);
    }
}
