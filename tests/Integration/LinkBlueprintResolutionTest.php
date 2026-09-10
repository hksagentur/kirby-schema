<?php

namespace Hks\Schema\Integration;

use Hks\Schema\Data\Link;
use Hks\Schema\Data\Types\LinkTarget;
use Hks\Schema\TestCase;
use Kirby\Cms\StructureObject;

class LinkBlueprintResolutionTest extends TestCase
{
    use InteractsWithBlueprintFields;

    public function testToDataObjectResolvesALinkFromContentShapedByItsOwnBlueprint(): void
    {
        $content = static::contentFromBlueprint('@hksagentur/schema/fields/link', [
            'title' => 'Example',
            'url' => 'https://example.com',
            'target' => '_blank',
            'rel' => 'bookmark',
        ]);

        $structureObject = new StructureObject(['content' => $content]);

        $link = $structureObject->toDataObject('link');

        $this->assertInstanceOf(Link::class, $link);
        $this->assertSame('Example', $link->title());
        $this->assertSame('https://example.com', $link->url());
        $this->assertSame(LinkTarget::Blank, $link->target());
        $this->assertSame('bookmark', $link->rel());
    }

    public function testActionsStructureRowResolvesToALinkTheSameWayAsTheStandaloneField(): void
    {
        // `actions.yml`'s `fields: { extends: link }` merges `link.yml`'s fields directly into
        // the structure row, the same "extends" pattern `person.yml` already uses — so a row
        // resolves through the exact same `Link::fromArray()` shape as `link.yml` on its own.
        $content = static::contentFromBlueprint('@hksagentur/schema/fields/actions', [
            'title' => 'Example',
            'url' => 'https://example.com',
        ]);

        $link = (new StructureObject(['content' => $content]))->toDataObject('link');

        $this->assertInstanceOf(Link::class, $link);
        $this->assertSame('Example', $link->title());
        $this->assertSame('https://example.com', $link->url());
    }
}
