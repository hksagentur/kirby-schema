<?php

namespace Hks\Schema\Data;

use Hks\Schema\Data\Types\EmailAddress;
use Hks\Schema\Data\Types\GeoCoordinate;
use Hks\Schema\Data\Types\PhoneNumber;
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

    public function testToHtmlRendersAnHCardAlongsideTheMicrodata(): void
    {
        $html = (new Organization(
            name: 'Acme Inc.',
            address: PostalAddress::from([
                'streetAddress' => 'Main Street 1',
                'postalCode' => '12345',
                'addressLocality' => 'Springfield',
                'addressCountry' => 'DE',
            ]),
            email: new EmailAddress('info@acme.example'),
            telephone: new PhoneNumber('+49 30 123456'),
        ))->toHtml(['class' => 'colophon']);

        $this->assertStringContainsString('class="h-card colophon"', $html);
        $this->assertStringContainsString('class="p-name p-org"', $html);
        $this->assertStringContainsString('class="h-adr p-adr"', $html);
        $this->assertStringContainsString('<span class="p-locality" itemprop="addressLocality">Springfield</span>', $html);
        $this->assertStringContainsString('class="u-email"', $html);
        $this->assertStringContainsString('class="p-tel"', $html);
    }

    public function testResolvesContactPointsStoredAsYaml(): void
    {
        $organization = Organization::fromArray([
            'name' => 'Acme Inc.',
            'contactpoints' => "- contacttype: Customer service\n  telephone: +49 30 123456\n  email: service@acme.example",
        ]);

        $contactPoint = $organization->contactPoints()->first();

        $this->assertCount(1, $organization->contactPoints());

        $this->assertSame('Customer service', $contactPoint->type());
        $this->assertSame('+49 30 123456', $contactPoint->telephone()?->value);
        $this->assertSame('service@acme.example', $contactPoint->email()?->value);
    }

    public function testToStructuredDataListsContactPoints(): void
    {
        $organization = Organization::fromArray([
            'name' => 'Acme Inc.',
            'contactPoints' => [
                ['contactType' => 'Sales', 'email' => 'sales@acme.example'],
            ],
        ]);

        $this->assertSame([
            [
                '@type' => 'ContactPoint',
                'contactType' => 'Sales',
                'email' => 'sales@acme.example',
            ],
        ], $organization->toStructuredData()['contactPoint']);

        $this->assertArrayNotHasKey('contactPoint', (new Organization(name: 'Acme Inc.'))->toStructuredData());
        $this->assertStringContainsString('itemprop="contactPoint"', $organization->toHtml());
    }

    public function testResolvesTheAlternateName(): void
    {
        $organization = Organization::fromArray(['name' => 'Acme Inc.', 'alternatename' => 'Acme']);

        $this->assertSame('Acme', $organization->alternateName());
        $this->assertSame('Acme', $organization->toStructuredData()['alternateName']);
        $this->assertNull(Organization::fromArray(['name' => 'Acme Inc.'])->alternateName());
    }
}
