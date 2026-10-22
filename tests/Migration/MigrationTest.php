<?php

namespace Hks\Schema\Migration;

use Hks\Schema\Concerns\InteractsWithTmp;
use Hks\Schema\TestCase;
use Kirby\Cms\App;
use Kirby\Cms\ModelWithContent;
use Kirby\Data\Data;
use Kirby\Toolkit\Str;

class MigrationTest extends TestCase
{
    use InteractsWithTmp;

    public const TMP = __DIR__ . '/tmp/MigrationTest';

    protected function setUp(): void
    {
        parent::setUp();

        $this->app = new App([
            'roots' => ['index' => static::TMP],
            'blueprints' => [
                'site' => [
                    'fields' => [
                        'company' => ['type' => 'object'],
                        'channels' => ['type' => 'structure'],
                        'blocks' => ['type' => 'blocks'],
                        'layout' => ['type' => 'layout'],
                    ],
                ],
                'pages/employee' => [
                    'fields' => [
                        'qualifications' => ['type' => 'entries'],
                        'expertise' => ['type' => 'entries'],
                    ],
                ],
            ],
            'site' => [
                'content' => [
                    'company' => Data::encode(['streetaddress' => 'Main Street 1', 'locality' => 'Achim'], 'yaml'),
                    'channels' => Data::encode([['url' => '#a', 'target' => 'true'], ['url' => '#b', 'target' => 'false']], 'yaml'),
                    'blocks' => Data::encode([
                        ['type' => 'cards', 'content' => ['link' => ['url' => '#c', 'target' => 'true']]],
                        ['type' => 'text', 'isHidden' => true, 'content' => ['text' => 'Draft']],
                    ], 'json'),
                    'layout' => Data::encode([[
                        'id' => 'l1',
                        'attrs' => ['class' => 'wide'],
                        'columns' => [[
                            'id' => 'c1',
                            'width' => '1/1',
                            'blocks' => [
                                ['id' => 'b1', 'type' => 'cards', 'content' => ['link' => ['url' => '#d', 'target' => 'true']]],
                                ['id' => 'b2', 'type' => 'text', 'isHidden' => true, 'content' => ['text' => 'Draft']],
                            ],
                        ]],
                    ]], 'json'),
                ],
                'children' => [
                    [
                        'slug' => 'jane',
                        'template' => 'employee',
                        'content' => ['occupation' => 'Carpenter, Mason', 'responsibilities' => ''],
                    ],
                    [
                        'slug' => 'house',
                        'content' => ['occupation' => 'Not a person'],
                    ],
                ],
            ],
        ]);
    }

    public function testRenameFieldsOfAnObject(): void
    {
        $changes = (new RenameCompanyAddress())->migrate(Changeset::of($this->app->site()->content()))->toArray();

        $this->assertSame(['streetaddress' => 'Main Street 1', 'addresslocality' => 'Achim'], Data::decode($changes['company'], 'yaml'));
    }

    public function testChangeTheRowsOfAStructureAndTheBlocksOfABlocksField(): void
    {
        $changes = (new ConvertLinkTargets())->migrate(Changeset::of($this->app->site()->content()))->toArray();
        $blocks = Data::decode($changes['blocks'], 'json');

        $this->assertSame([['url' => '#a', 'target' => '_blank'], ['url' => '#b']], Data::decode($changes['channels'], 'yaml'));
        $this->assertSame('_blank', $blocks[0]['content']['link']['target']);
        $this->assertCount(2, $blocks, 'hidden blocks are kept');
        $this->assertTrue($blocks[1]['isHidden']);
    }

    public function testChangeTheBlocksOfALayoutField(): void
    {
        $changes = (new ConvertLinkTargets())->migrate(Changeset::of($this->app->site()->content()))->toArray();
        $layout = Data::decode($changes['layout'], 'json')[0];

        $this->assertSame(['class' => 'wide'], $layout['attrs']);
        $this->assertSame('_blank', $layout['columns'][0]['blocks'][0]['content']['link']['target']);
        $this->assertTrue($layout['columns'][0]['blocks'][1]['isHidden'], 'hidden blocks are kept');
    }

    public function testRenameAndConvertTopLevelFields(): void
    {
        $changes = (new MovePersonTags())->migrate(Changeset::of($this->app->page('jane')->content()));

        $this->assertSame(['occupation', 'responsibilities', 'qualifications'], $changes->dirty(), 'no empty expertise field');
        $this->assertSame([
            'occupation' => null,
            'responsibilities' => null,
            'qualifications' => Data::encode(['Carpenter', 'Mason'], 'yaml'),
        ], $changes->toArray());
    }

    public function testAppliesToLimitsTheModels(): void
    {
        $migration = new MovePersonTags();

        $this->assertTrue($migration->appliesTo($this->app->page('jane')));
        $this->assertFalse($migration->appliesTo($this->app->page('house')));
    }

    public function testAMigratedModelHasNoMoreChanges(): void
    {
        $site = $this->app->site();
        $migrated = $site->clone(['content' => [
            ...$site->content()->toArray(),
            ...(new ConvertLinkTargets())->migrate(Changeset::of($site->content()))->toArray(),
        ]]);

        $this->assertTrue((new ConvertLinkTargets())->migrate(Changeset::of($migrated->content()))->isEmpty());
    }

    public function testToArrayEncodesBlocksAsJsonAndOtherValuesAsYaml(): void
    {
        $changes = Changeset::of($this->app->site()->content())
            ->set('blocks', [['type' => 'text', 'content' => []]])
            ->set('channels', [['url' => '#']])
            ->set('title', 'Unchanged')
            ->toArray();

        $this->assertStringStartsWith('[{', $changes['blocks']);
        $this->assertSame([['url' => '#']], Data::decode($changes['channels'], 'yaml'));
        $this->assertStringStartsNotWith('[', $changes['channels']);
        $this->assertSame('Unchanged', $changes['title']);
    }

    public function testRunReportsTheChangesOfADryRun(): void
    {
        $messages = [];

        $updated = (new MovePersonTags())->run(
            function (string $message, bool $error) use (&$messages) {
                $messages[] = $message;
            },
            dryRun: true,
        );

        $this->assertSame(1, $updated);
        $this->assertSame(['jane (en): occupation, responsibilities, qualifications'], $messages);
    }

    public function testRunSavesRenamedFieldsInPlace(): void
    {
        $this->app->page('jane')->version('latest')->save([
            'occupation' => 'Carpenter, Mason',
            'note' => 'Since 2020',
        ], 'en', overwrite: true);

        $updated = (new MovePersonTags())->run(fn (string $message, bool $error) => null);

        $fields = $this->app->page('jane')->version('latest')->read('en');

        $this->assertSame(1, $updated);
        $this->assertSame(['qualifications', 'note'], array_keys($fields));
        $this->assertSame(['Carpenter', 'Mason'], Data::decode($fields['qualifications'], 'yaml'));
    }
}

class RenameCompanyAddress extends Migration
{
    public function migrate(Changeset $changeset): Changeset
    {
        return $changeset->object('company', fn (Changeset $company) => $company
            ->rename('locality', 'addressLocality')
            ->rename('country', 'addressCountry'));
    }
}

class ConvertLinkTargets extends Migration
{
    public function migrate(Changeset $changeset): Changeset
    {
        return $changeset
            ->structure('channels', fn (Changeset $link) => $this->convertTarget($link))
            ->blocks('blocks', $this->convertBlock(...))
            ->layouts('layout', $this->convertBlock(...));
    }

    protected function convertBlock(Changeset $block): Changeset
    {
        return $block->object('link', fn (Changeset $link) => $this->convertTarget($link));
    }

    protected function convertTarget(Changeset $link): Changeset
    {
        return match ($link->content()->get('target')->value()) {
            true, 'true' => $link->set('target', '_blank'),
            false, 'false', '' => $link->remove('target'),
            default => $link,
        };
    }
}

class MovePersonTags extends Migration
{
    public function appliesTo(ModelWithContent $model): bool
    {
        return $model->blueprint()->field('qualifications') !== null;
    }

    public function migrate(Changeset $changeset): Changeset
    {
        $toEntries = fn (?string $tags) => Str::split((string) $tags, ',');

        return $changeset
            ->rename('occupation', 'qualifications', $toEntries)
            ->rename('responsibilities', 'expertise', $toEntries);
    }
}
