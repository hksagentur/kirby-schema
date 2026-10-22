<?php

namespace Hks\Schema\Integration;

use Hks\Schema\TestCase;
use Kirby\Cms\Blueprint;
use Kirby\Filesystem\Dir;
use Kirby\Filesystem\F;

class BlueprintOverrideTest extends TestCase
{
    use InteractsWithBlueprintFields;

    protected string $root;

    protected function setUp(): void
    {
        parent::setUp();

        $this->root = sys_get_temp_dir() . '/kirby-schema-blueprints-' . uniqid();

        Blueprint::$loaded = [];
    }

    protected function tearDown(): void
    {
        Dir::remove($this->root);

        Blueprint::$loaded = [];

        parent::tearDown();
    }

    public function testBlueprintsReferenceRegisteredPublicNames(): void
    {
        $blueprints = $this->app->extensions('blueprints');

        foreach (glob(dirname(__DIR__, 2) . '/config/blueprints/*/*.yml') as $file) {
            $yaml = F::read($file);

            $this->assertStringNotContainsString('@hksagentur/', $yaml, F::filename($file) . ' references an original');

            preg_match_all('#schema/[a-z]+/[a-z-]+#', $yaml, $matches);

            foreach ($matches[0] as $name) {
                $this->assertArrayHasKey($name, $blueprints, sprintf('%s references %s', F::filename($file), $name));
            }
        }
    }

    public function testPublicNameExtendsTheOriginal(): void
    {
        $this->assertSame(
            static::resolvedBlueprintFieldNames('@hksagentur/schema/fields/link'),
            array_keys(Blueprint::fieldsProps(Blueprint::extend('schema/fields/link')['fields'])),
        );
    }

    public function testSiteBlueprintExtendingTheOriginalReachesTheBlocks(): void
    {
        $this->siteBlueprint('schema/fields/link', <<<'YAML'
            extends: '@hksagentur/schema/fields/link'
            fields:
              icon:
                type: text
            YAML);

        $this->assertContains('icon', $this->cardLinkFieldNames());
        $this->assertContains('url', $this->cardLinkFieldNames());
    }

    public function testSiteBlueprintWithoutExtendsReplacesTheOriginal(): void
    {
        $this->siteBlueprint('schema/fields/link', <<<'YAML'
            type: group
            fields:
              url:
                type: url
            YAML);

        $this->assertSame(['url'], $this->cardLinkFieldNames());
    }

    protected function siteBlueprint(string $name, string $yaml): void
    {
        F::write("{$this->root}/{$name}.yml", $yaml);

        $this->app = $this->app->clone([
            'roots' => [
                'blueprints' => $this->root,
            ],
        ]);
    }

    protected function cardLinkFieldNames(): array
    {
        $card = Blueprint::extend('schema/blocks/card');
        $link = $card['tabs']['content']['fields']['link'];

        return array_keys(Blueprint::fieldsProps($link['fields']));
    }
}
