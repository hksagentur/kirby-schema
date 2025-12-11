<?php

namespace Hks\Schema\Formatter;

use Hks\Schema\Data\Organization;

/**
 * @extends MicroformatFormatter<Organization>
 */
class OrganizationMicroformatFormatter extends MicroformatFormatter
{
    public function getMicroformatType(): string
    {
        return 'h-card';
    }

    public function getAttributes(mixed $organization): array
    {
        return [
            'name' => fn () => $organization->name()?->toHtml(['class' => ['p-name', 'p-org']]),
            'address' => fn () => $organization->address()?->format(PostalAddressMicroformatFormatter::class, $this->option('address', [])),
            'coordinates' => fn () => $organization->coordinates()?->format(GeoCoordinateMicroformatFormatter::class, $this->option('coordiates', [])),
            'email' => fn () => $organization->email()?->toHtml(['class' => 'u-email']),
            'telephone' => fn () => $organization->telephone()?->toHtml(['class' => 'p-tel']),
            'fax' => fn () => $organization->fax()?->toHtml(['class' => 'p-tel-fax']),
            'website' => fn () => $organization->website()?->toHtml(['class' => 'u-url']),
        ];
    }
}
