<?php

namespace Hks\Schema\Formatter;

use Hks\Schema\Data\Organization;
use Kirby\Toolkit\I18n;

/**
 * @extends StructuredDataFormatter<Organization>
 */
class OrganizationTextFormatter extends StructuredDataFormatter
{
    public function getAttributes(mixed $organization): array
    {
        return [
            'name' => fn () => $organization->name()?->toString(),
            'address' => fn () => $organization->address()?->toString(),
            'coordinates' => fn () => $organization->coordinates()?->toString(),
            'email' => fn () => $organization->email()?->toString(),
            'telephone' => fn () => $organization->telephone()?->toString(),
            'fax' => fn () => $organization->fax()?->toString(),
            'website' => fn () => $organization->website()?->toString(),
        ];
    }

    public function compose(array $content): string
    {
        return I18n::template('hksagentur.schema.formatters.organization', replace: $content);
    }
}
