<?php

namespace Hks\Schema\Data;

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
}
