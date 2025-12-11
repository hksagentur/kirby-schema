<?php

namespace Hks\Schema\Transformer;

use Hks\Schema\Data\Organization;

/**
 * @extends Transformer<Organization>
 */
class OrganizationSchemaTransformer extends Transformer
{
    public function transform(object $organization): array
    {
        return array_filter([
            '@type' => 'Organization',
            'address' => $organization->address()?->toSchema(),
            'coordinates' => $organization->coordinates()?->toSchema(),
            'email' => $organization->email()?->toString(),
            'telephone' => $organization->telephone()?->toString(),
            'faxNumber' => $organization->fax()?->toString(),
            'sameAs' => $organization->website()?->toString(),
        ]);
    }
}
