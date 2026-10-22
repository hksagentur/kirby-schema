<?php

namespace Hks\Schema\Migration;

use Hks\Schema\TestCase;
use Kirby\Cms\Page;
use Kirby\Content\Content;
use Kirby\Content\Field;
use Kirby\Data\Data;

class ChangesetTest extends TestCase
{
    protected function changes(): Changeset
    {
        return Changeset::of(new Content(['title' => 'Home', 'locality' => 'Achim']));
    }

    public function testANewChangesObjectIsEmpty(): void
    {
        $this->assertTrue($this->changes()->isEmpty());
        $this->assertSame([], $this->changes()->dirty());
    }

    public function testSetAndRemoveReturnNewObjects(): void
    {
        $changes = $this->changes();
        $changed = $changes->set('Title', 'Start');

        $this->assertTrue($changes->isEmpty());
        $this->assertSame(['title' => 'Start'], $changed->toArray());
    }

    public function testRemoveIsDistinctFromSettingNull(): void
    {
        $this->assertSame(['title' => null], $this->changes()->remove('title')->toArray());
        $this->assertSame(['title'], $this->changes()->remove('title')->dirty());
        $this->assertArrayNotHasKey('title', $this->changes()->remove('title')->apply());
        $this->assertArrayHasKey('title', $this->changes()->set('title', null)->apply());
    }

    public function testSetAfterRemoveRestoresTheField(): void
    {
        $this->assertSame(['title' => 'Start'], $this->changes()->remove('title')->set('title', 'Start')->toArray());
    }

    public function testRenameOnlyChangesAnExistingField(): void
    {
        $this->assertSame(
            ['locality' => null, 'addresslocality' => 'ACHIM'],
            $this->changes()->rename('locality', 'addressLocality', fn (string $value) => strtoupper($value))->toArray(),
        );
        $this->assertTrue($this->changes()->rename('missing', 'other')->isEmpty());
    }

    public function testApplyReturnsTheChangedContent(): void
    {
        $this->assertSame(
            ['title' => 'Home', 'addresslocality' => 'Achim'],
            $this->changes()->rename('locality', 'addressLocality')->apply(),
        );
    }

    public function testRenameKeepsThePositionOfTheField(): void
    {
        $changes = Changeset::of(new Content(['locality' => 'Achim', 'title' => 'Home']))
            ->rename('locality', 'addressLocality')
            ->set('country', 'DE');

        $this->assertSame(
            ['addresslocality' => 'Achim', 'title' => 'Home', 'country' => 'DE'],
            $changes->apply(),
        );
        $this->assertSame(
            ['uuid' => 'x', 'addresslocality' => 'Achim', 'title' => 'Home', 'country' => 'DE'],
            $changes->applyTo(['uuid' => 'x', 'locality' => 'Achim', 'title' => 'Home']),
        );
    }

    public function testModelReturnsTheParentOfTheContent(): void
    {
        $page = new Page(['slug' => 'home']);

        $this->assertSame($page, Changeset::of(new Content([], $page))->model());
    }

    public function testToArrayEncodesAFieldTheBlueprintDoesNotDefineAsYaml(): void
    {
        $changes = Changeset::of(new Content([], new Page(['slug' => 'home'])))->set('leftover', ['a' => 1]);

        $this->assertSame("a: 1\n", $changes->toArray()['leftover']);
    }

    public function testNestedStructuresAndObjectsInsideABlock(): void
    {
        $page = new Page(['slug' => 'home', 'blueprint' => ['fields' => ['blocks' => ['type' => 'blocks']]], 'content' => [
            'blocks' => Data::encode([[
                'type' => 'list',
                'content' => [
                    'items' => [['url' => '#', 'target' => 'true']],
                    'meta' => ['locality' => 'Achim', 'postalcode' => '1'],
                ],
            ]], 'json'),
        ]]);

        $changes = Changeset::of($page->content())->blocks('blocks', fn (Changeset $block) => $block
            ->structure('items', fn (Changeset $row) => $row->set('target', '_blank'))
            ->object('meta', fn (Changeset $meta) => $meta->rename('locality', 'addressLocality')));

        $content = Data::decode($changes->toArray()['blocks'], 'json')[0]['content'];

        $this->assertSame([['url' => '#', 'target' => '_blank']], $content['items']);
        $this->assertSame(['addresslocality' => 'Achim', 'postalcode' => '1'], $content['meta']);
    }

    public function testSetIgnoresTheValueTheContentAlreadyHolds(): void
    {
        $this->assertTrue($this->changes()->set('title', 'Home')->isEmpty());
        $this->assertTrue($this->changes()->set('title', 'Start')->set('title', 'Home')->isEmpty());
    }

    public function testSetDoesNotCreateAnEmptyField(): void
    {
        $this->assertTrue($this->changes()->set('missing', '')->isEmpty());
        $this->assertTrue($this->changes()->set('missing', [])->isEmpty());
    }

    public function testSetComparesAnArrayWithTheDecodedField(): void
    {
        $changes = Changeset::of(new Content(['related' => "- page://a\n- page://b\n"]));

        $this->assertTrue($changes->set('related', ['page://a', 'page://b'])->isEmpty());
        $this->assertFalse($changes->set('related', ['page://a'])->isEmpty());
    }

    public function testTransformDerivesTheValueFromTheField(): void
    {
        $changes = Changeset::of(new Content(['tags' => 'Altbau, Neubau', 'related' => "- page://old\n"]))
            ->transform('tags', fn (Field $tags) => implode(', ', str_replace('Altbau', 'Bestand', $tags->split())))
            ->transform('related', fn (Field $related) => str_replace('page://old', 'page://new', $related->yaml()));

        $this->assertSame(['tags' => 'Bestand, Neubau', 'related' => "- page://new\n"], $changes->toArray());
    }

    public function testTransformWithoutAnEffectLeavesTheChangesetEmpty(): void
    {
        $changes = Changeset::of(new Content(['tags' => 'Neubau']))
            ->transform('tags', fn (Field $tags) => implode(', ', str_replace('Altbau', 'Bestand', $tags->split())));

        $this->assertTrue($changes->isEmpty());
    }
}
