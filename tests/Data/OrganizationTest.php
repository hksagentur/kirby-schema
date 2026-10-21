<?php

namespace Hks\Schema\Data;

use Hks\Schema\Data\Types\GeoCoordinate;
use Hks\Schema\Data\Types\PostalAddress;
use Hks\Schema\Data\Types\Url;
use Hks\Schema\TestCase;

class OrganizationTest extends TestCase
{
    public function testToStructuredDataRendersTheWebsiteUnderTheSchemaOrgUrlKey(): void
    {
        $organization = new Organization(
            name: 'Acme Inc.',
            website: Url::from('https://acme.example'),
        );

        $data = $organization->toStructuredData();

        $this->assertSame('https://acme.example', $data['url']);
        $this->assertArrayNotHasKey('website', $data);
    }

    public function testToStructuredDataDescribesTheLocationAsAPlace(): void
    {
        $organization = new Organization(
            name: 'Acme Inc.',
            address: PostalAddress::from(['streetAddress' => 'Main Street 1', 'addressLocality' => 'Achim']),
            coordinates: GeoCoordinate::from(['latitude' => 53.0, 'longitude' => 9.0]),
        );

        $data = $organization->toStructuredData();

        $this->assertSame($organization->address()->toStructuredData(), $data['address']);
        $this->assertSame([
            '@type' => 'Place',
            'address' => $organization->address()->toStructuredData(),
            'geo' => $organization->coordinates()->toStructuredData(),
        ], $data['location']);
    }

    public function testToStructuredDataOmitsTheLocationWithoutAddressAndCoordinates(): void
    {
        $this->assertArrayNotHasKey('location', (new Organization(name: 'Acme Inc.'))->toStructuredData());
    }

    public function testToHtmlUsesTheSameDefaultTypeAsTheStructuredData(): void
    {
        $organization = new Organization(name: 'Acme Inc.');

        $this->assertStringContainsString('itemtype="https://schema.org/Organization"', $organization->toHtml());
        $this->assertSame('Organization', $organization->toStructuredData()['@type']);
    }

    public function testToHtmlAcceptsAMoreSpecificType(): void
    {
        $html = (new Organization(name: 'Acme Inc.'))->toHtml(['itemtype' => 'https://schema.org/LocalBusiness']);

        $this->assertStringContainsString('itemtype="https://schema.org/LocalBusiness"', $html);
        $this->assertStringNotContainsString('schema.org/Organization', $html);
    }
}
