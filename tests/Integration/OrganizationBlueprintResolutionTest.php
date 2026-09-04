<?php

namespace Hks\Schema\Integration;

use Hks\Schema\Data\Organization;
use Hks\Schema\TestCase;
use Kirby\Cms\Page;

class OrganizationBlueprintResolutionTest extends TestCase
{
    use InteractsWithBlueprintFields;

    public function testToDataObjectResolvesAnOrganizationFromContentShapedByItsOwnBlueprint(): void
    {
        $content = static::contentFromBlueprint('@hksagentur/schema/fields/organization', [
            'name' => 'Acme GmbH',
            'alternateName' => 'Acme',
            'streetAddress' => 'Musterstraße 1',
            'postalCode' => '12345',
            'addressLocality' => 'Berlin',
            'addressCountry' => 'DE',
            'latitude' => '52.520008',
            'longitude' => '13.404954',
            'telephone' => '+49 30 1234567',
            'email' => 'info@acme.example',
            'website' => 'https://acme.example',
        ]);

        $page = new Page(['slug' => 'acme', 'content' => $content]);

        $organization = $page->toDataObject('organization');

        $this->assertInstanceOf(Organization::class, $organization);
        $this->assertSame('Acme GmbH', $organization->name());
        $this->assertSame('Musterstraße 1', $organization->address()?->streetAddress());
        $this->assertSame('Berlin', $organization->address()?->locality());
        $this->assertSame('DE', $organization->address()?->country());
        $this->assertSame(52.520008, $organization->coordinates()?->latitude());
        $this->assertSame(13.404954, $organization->coordinates()?->longitude());
        $this->assertSame('info@acme.example', $organization->email()?->value);
        $this->assertSame('https://acme.example', $organization->website()?->value);
    }
}
