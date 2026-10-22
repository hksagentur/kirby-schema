<?php

namespace Hks\Schema\Cms;

use Hks\Schema\TestCase;

class BlueprintTest extends TestCase
{
    public function testVendorAndPackageSplitTheNamespace(): void
    {
        $this->assertSame('hksagentur', Blueprint::vendor('@hksagentur/schema'));
        $this->assertSame('schema', Blueprint::package('@hksagentur/schema'));
    }

    public function testVendorIsNullWithoutOne(): void
    {
        $this->assertNull(Blueprint::vendor('schema'));
        $this->assertSame('schema', Blueprint::package('schema'));
    }

    public function testNamespaceRegistersTheOriginalAndThePublicName(): void
    {
        $blueprints = Blueprint::namespace('@hksagentur/schema', dirname(__DIR__, 2) . '/config/blueprints');

        $this->assertStringEndsWith('/fields/link.yml', $blueprints['@hksagentur/schema/fields/link']);
        $this->assertSame(['extends' => '@hksagentur/schema/fields/link'], $blueprints['schema/fields/link']);
    }
}
