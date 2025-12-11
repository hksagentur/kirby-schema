<?php

namespace Hks\Schema\Transformer;

use Hks\Schema\Data\PostalAddress;

/**
 * @extends Transformer<PostalAddress>
 */
class PostalAddressSchemaTransformer extends Transformer
{
    public function transform(object $postalAddress): array
    {
        return array_filter([
            '@type' => 'PostalAddress',
            'streetAddress' => $postalAddress->streetAddress(),
            'postalCode' => $postalAddress->postalCode(),
            'locality' => $postalAddress->locality(),
            'addressCountry' => $postalAddress->country(),
        ]);
    }
}
