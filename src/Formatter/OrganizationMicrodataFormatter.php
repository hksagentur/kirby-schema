<?php

namespace Hks\Schema\Formatter;

use Hks\Schema\Data\Organization;

/**
 * @extends MicrodataFormatter<Organization>
 */
class OrganizationMicrodataFormatter extends MicrodataFormatter
{
    public function getSchemaType(): string
    {
        return 'https://schema.org/Organization';
    }

    public function getAttributes(mixed $organization): array
    {
        return [
            'name' => fn () => $organization->name()?->toHtml(['itemprop' => 'name']),
            'address' => fn () => $organization->address()?->format(PostalAddressMicrodataFormatter::class, $this->option('address', []), ),
            'coordinates' => fn () => $organization->coordinates()?->format(GeoCoordinateMicrodataFormatter::class, $this->option('coordinates', [])),
            'email' => fn () => $organization->email()?->toHtml(['itemprop' => 'email']),
            'telephone' => fn () => $organization->telephone()?->toHtml(['itemprop' => 'telephone']),
            'fax' => fn () => $organization->fax()?->toHtml(['itemprop' => 'faxNumber']),
            'website' => fn () => $organization->website()->toHtml(['itemprop' => 'url']),
        ];
    }
}
