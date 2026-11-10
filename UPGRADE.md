# Upgrade Guide

## Upgrading to 2.0 from 1.x

v2 is a rewrite and not a drop-in upgrade. Changes that touch stored content need a
[content migration](#content-migrations); everything else is an update to blueprints or PHP code.

### High impact changes

- [Updating dependencies](#updating-dependencies)
- [Address fields](#address-fields)
- [Link target](#link-target)
- [Link text](#link-text)
- [Person tags](#person-tags)
- [Model traits](#model-traits)
- [Responsive images and videos](#responsive-images-and-videos)

### Medium impact changes

- [Opening hours fields](#opening-hours-fields)
- [Field methods](#field-methods)
- [Links of `HasLink` models](#links-of-haslink-models)
- [Block accessors](#block-accessors)
- [Image and video options](#image-and-video-options)
- [Video formats and tracks](#video-formats-and-tracks)

### Low impact changes

- [Blueprint names](#blueprint-names)
- [Blueprint aliases](#blueprint-aliases)
- [Blueprint class](#blueprint-class)
- [Formatter and factory classes](#formatter-and-factory-classes)
- [National phone numbers](#national-phone-numbers)
- [Suggested packages](#suggested-packages)

## Content migrations

Where content lives depends on the project — a link may sit in a navigation structure on the site,
in a block, or in a field of a page — so the plugin doesn't migrate content on its own. A project
writes one small migration per change for its own fields with `Hks\Schema\Migration\Migration` and
`Hks\Schema\Migration\Changeset`:

```php
<?php

namespace App\Migrations;

use Hks\Schema\Migration\Changeset;
use Hks\Schema\Migration\Migration;
use Kirby\Cms\ModelWithContent;
use Kirby\Toolkit\Str;

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
            ->structure('navigation', fn (Changeset $link) => $this->convertTarget($link))
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
```

`migrate()` receives an immutable `Changeset` for a model's content in one language and returns it
with the changes it describes; `appliesTo()` limits the models a migration runs on.

- `set()`, `remove()` and `rename()` (optionally converting the value) change fields. `set()`
  records nothing if the content already holds the value.
- `transform()` sets a field to what a callback derives from the current Kirby `Field`, e.g. for
  list fields: `split()` reads `tags`, `multiselect` and `checkboxes`, `yaml()` reads `pages`,
  `files`, `users` and `entries`.
- `blocks()`, `layouts()`, `structure()` and `object()` pass a `Changeset` for each block (hidden
  ones included), each structure row or the object to a callback, with the block or row as the
  second argument.
- `content()` reads the content, `model()` returns the model it belongs to.

Register the migrations and run them:

```php
// site/config/config.php
return [
    'hksagentur.schema.migrations' => [
        App\Migrations\ConvertLinkTargets::class,
        App\Migrations\RenameCompanyAddress::class,
        App\Migrations\MovePersonTags::class,
    ],
];
```

```bash
kirby schema:migrate --dry-run
kirby schema:migrate
```

`schema:migrate` runs every migration for every model in every language and saves each model once.
`--dry-run` only lists the fields that would change; models with unsaved changes in the Panel are
skipped. There is no record of which migrations ran — content gets copied between environments and
restored from backups — so a migration must find nothing to change once its content is migrated,
and running it again is safe.

## High impact changes

### Updating dependencies

**Likelihood of impact: High**

Update the requirements in the project's `composer.json`:

- `hksagentur/kirby-schema` to `^2.0`
- `getkirby/cms` to `^5.1` (was `>=4.0`)
- `hksagentur/kirby-media-kit` as a direct dependency if the project renders responsive images or
  videos — v1 installed it, v2 no longer does; `^2.3` if it uses key visuals (`keyVisual()`)

PHP `>=8.3` is unchanged.

### Address fields

**Likelihood of impact: High** — requires a content migration

The fields of `fields/address` are named after their schema.org properties now, and there is a new
`addressRegion` field:

| v1 field   | v2 field          |
| ---------- | ----------------- |
| `locality` | `addressLocality` |
| `country`  | `addressCountry`  |

`address.yml` is a `group`, so the renamed keys live wherever a project embeds it: on a page, in an
`object` field such as a site-wide organization, in structure rows, or in blocks. `organization.yml`
and `place.yml` embed it too. Migrate the content (see `RenameCompanyAddress` above) and update
templates:

```php
// Before
<?= $site->company()->toObject()->locality() ?>

// After
<?= $site->company()->toObject()->addressLocality() ?>
<?= $site->company()->toDataObject('organization')?->address()?->locality() ?>
```

### Link target

**Likelihood of impact: High** — requires a content migration

The `target` of `fields/link` (and `fields/actions`) was a toggle storing `true`/`false`. It's a
`toggles` field with the options `_self`, `_blank`, `_parent` and `_top` now. Resolving v1 content
through `toDataObject('link')` throws a `TypeError` until it's migrated: `true` becomes `_blank`,
`false` removes the key (`_self` is the default). See `ConvertLinkTargets` above.

```php
// Before
'target' => $link->target()->isTrue() ? '_blank' : null,

// After
'target' => $link->target()->or('_self')->value() === '_self' ? null : $link->target()->value(),
```

### Link text

**Likelihood of impact: High** — requires a content migration

The visible text of `fields/link` (and `fields/actions`, `fields/channels`, `fields/navigation`) is
stored as `text` instead of `title`, as in Kirby's `(link: … text: …)` tag, where `title` is the
`title` attribute. `Link::title()` is now `Link::text()`, which falls back to the title of the linked
page when the text is empty; the constructor argument is `text`. Rename the key wherever a project
stores links, e.g. with `->rename('title', 'text')` on each link in a migration like
`ConvertLinkTargets` above; mind that blocks have a `title` of their own, so rename it only on the
link objects and rows, not on the block.

```php
// Before
<?= esc($link->title() ?? $link->url()) ?>

// After
<?= esc($link->text() ?? $link->url()) ?>
```

### Person tags

**Likelihood of impact: High** — requires a content migration

`fields/person` replaces the two `tags` fields `occupation` and `responsibilities` with three
fields that map to schema.org:

| v1 field                    | v2 field                                    |
| --------------------------- | ------------------------------------------- |
| *(none)*                    | `jobTitle` (`text`)                         |
| `occupation` (`tags`)       | `qualifications` (`entries`)                |
| `responsibilities` (`tags`) | `expertise` (`entries`)                     |

`MovePersonTags` above moves each comma-separated tag into the matching list. Review the result:
v1 content often used `occupation` for a job title — move those values to `jobTitle` by hand.

### Model traits

**Likelihood of impact: High** for projects with page or block models

The traits moved to `Hks\Schema\Cms\Concerns` and are named after their primary method:

| v1 trait                         | v2 trait                                              |
| -------------------------------- | ----------------------------------------------------- |
| `Hks\Schema\Cms\HasAction`       | `Concerns\HasLink`                                    |
| `Hks\Schema\Cms\HasBlockContent` | `Concerns\HasBlocks` (key visuals: see below)         |
| `Hks\Schema\Cms\HasCoverImage`   | `Concerns\HasCoverImage`                              |
| `Hks\Schema\Cms\HasMedia`        | `Concerns\HasMediaFile`                               |
| `Hks\Schema\Cms\HasImage`        | `Concerns\HasImage` (a single `image()`)              |
| `Hks\Schema\Cms\HasVideo`        | `Concerns\HasVideo` (a single `video()`)              |
| `Hks\Schema\Cms\HasImageGallery` | `Concerns\HasGalleryImages`                           |

```php
// Before
use Hks\Schema\Cms\HasBlockContent;

class ArticlePage extends Page
{
    use HasBlockContent;
}

// After
use Hks\MediaKit\Cms\Concerns\HasKeyVisual;
use Hks\Schema\Cms\Concerns\HasBlocks;

class ArticlePage extends Page
{
    use HasBlocks;
    use HasKeyVisual;
}
```

`images()`/`hasImages()` and `videos()`/`hasVideos()` are gone, as are `HasGeoCoordinates`,
`HasOpeningHours`, `HasPostalAddress` and `HasTags` — use `toDataType()`/`toDataObject()` instead.

### Responsive images and videos

**Likelihood of impact: High** for projects that render images

The file methods `toResponsiveImage()` and `toResponsiveVideo()` moved to
[`hksagentur/kirby-media-kit`](https://github.com/hksagentur/kirby-media-kit). Require it directly;
the methods keep their names, so templates don't change.

Key visuals moved there too. `Contracts\HasKeyVisual` and `Concerns\HasKeyVisual` are gone, and
`HeroBlock`/`MediaTextBlock` no longer implement `keyVisual()`. In media-kit, a block implements
`Hks\MediaKit\Cms\Contracts\HasKeyVisual` and returns its media as rendered (`Image|Video`) instead of
a `File`; a page uses `Hks\MediaKit\Cms\Concerns\HasKeyVisual`, whose `keyVisuals()` collects the
media the block marked with `priority()` and renders their preload links with `toPreloadLinks()`.
Where you need the file (e.g. to measure its brightness), use `original()`, or `poster()?->original()`
for a video. The old trait also read image and video blocks with `priority: high` on its own; that
fallback is gone, so every block that should be preloaded (image, video, hero, media text, …) needs a
block model in your project that implements the contract. Without one its preload link silently
disappears.

## Medium impact changes

### Opening hours fields

**Likelihood of impact: Medium**

| v1 field ID                                 | v2 field ID                                  |
| ------------------------------------------- | -------------------------------------------- |
| `@hksagentur/schema/fields/hours`           | `schema/fields/opening-hours`                |
| `@hksagentur/schema/fields/hours-day`       | `schema/fields/opening-hours-specification`  |
| `@hksagentur/schema/fields/hours-exception` | `schema/fields/opening-hours-exception`      |

The stored content is unchanged; update the references in the project's blueprints.

### Field methods

**Likelihood of impact: Medium**

`toIcon()`, `toStructuredLink()`, `toCurrency()`, `toNumber()`, `toDecimal()` and `toOpeningHours()`
are gone. Convert the field to a value object instead:

```php
// Before
$page->price()->toCurrency()
$page->hours()->toOpeningHours()

// After
$page->price()->toDataType('money')?->toLocaleString()
$page->openingHours()->toDataType('opening-hours')
```

### Links of `HasLink` models

**Likelihood of impact: Medium**

`HasLink::link()` returned a Kirby `Content` object; it returns a `Hks\Schema\Data\Link` or `null`
now.

```php
// Before
<a href="<?= $block->link()->url()->toUrl() ?>"><?= $block->link()->title() ?></a>

// After
<?= $block->link()?->toHtml() ?>
<a href="<?= $block->link()?->url() ?>"><?= esc($block->link()?->title() ?? '') ?></a>
```

### Block accessors

**Likelihood of impact: Medium**

The block classes return their fields as stored. Defaults come from the blueprints (`default:`),
which Kirby fills in when a block is created in the Panel; derived values move into the project's
block snippets:

- `level()` no longer falls back to a heading level, `CollectionBlock::source()`/`status()` no
  longer to `current`/`listed`, `MediaTextBlock::alignment()` no longer to `normal`, and `ratio()`
  on `GalleryBlock`, `ImageBlock` and `VideoBlock` no longer to `'auto'`. Blocks saved before a
  blueprint had the default can hold an empty value.
- `CardBlock::title()`, `eyebrow()` and `text()` no longer fall back to the linked page's title,
  category and excerpt; `ImageBlock::src()`, `alt()`, `caption()` and `VideoBlock::url()`,
  `caption()` no longer to the selected file's.
- `ImageBlock::thumb()`, `thumbOptions()`, `thumbDimensions()` and `thumbResizeStrategy()` are gone.

```php
// Before
<h3><?= $block->title() ?></h3>
<figcaption><?= $block->caption() ?></figcaption>
<div style="--ratio: <?= $block->ratio() ?>">

// After
<h3><?= $block->title()->or($block->target()?->title()) ?></h3>
<figcaption><?= $block->caption()->or($block->image()?->caption()) ?></figcaption>
<div style="--ratio: <?= $block->ratio()->or('auto') ?>">
```

### Image and video options

**Likelihood of impact: Medium**

`hksagentur.schema.image` and `hksagentur.schema.video` are no longer read; configure
`hksagentur.media-kit.image`/`.video` instead. v1 merged its options *under* media-kit's defaults,
so a `hksagentur.schema.image.formats` setting never took effect — check the rendered output before
moving it over.

### Video formats and tracks

**Likelihood of impact: Medium** for projects with video formats, subtitles or captions — requires
a content migration

The files of a video's `formats` get the template `video-format`, those of `subtitles` and
`captions` the template `video-track`. Their `file` fields only list files of that template;
`sections/videos` lists the formats along with the videos, `sections/documents` the tracks along
with the documents. Both sections filter with a `query` now, so they no longer upload or sort:
files are added through the fields that reference them (`fields/video`, `fields/document`, a
video's formats and tracks). Before, these files had no template, so no
section listed them and they couldn't be deleted in the Panel once their row was removed. Add the
file blueprints next to the project's `video` blueprint:

```yaml
# site/blueprints/files/video-format.yml
extends: schema/files/video-format

# site/blueprints/files/video-track.yml
extends: schema/files/video-track
```

Files uploaded before have no template: rows keep showing them, but the fields' pickers and the
media tab don't. Assign the templates with a migration:

```php
<?php

namespace App\Migrations;

use Hks\Schema\Migration\Changeset;
use Hks\Schema\Migration\Migration;
use Kirby\Cms\File;
use Kirby\Cms\ModelWithContent;

class AssignVideoFileTemplates extends Migration
{
    public const TEMPLATES = [
        'formats' => 'video-format',
        'subtitles' => 'video-track',
        'captions' => 'video-track',
    ];

    public function appliesTo(ModelWithContent $model): bool
    {
        return $model instanceof File && $this->templateFor($model) !== null;
    }

    public function migrate(Changeset $changeset): Changeset
    {
        if ($changeset->content()->get('template')->or('default')->value() !== 'default') {
            return $changeset;
        }

        return $changeset->set('template', $this->templateFor($changeset->model()));
    }

    protected function templateFor(File $file): ?string
    {
        foreach ($file->siblings(false)->template('video') as $video) {
            foreach (static::TEMPLATES as $field => $template) {
                foreach ($video->content()->get($field)->toStructure() as $row) {
                    if ($file->kirby()->file($row->file()->yaml()[0] ?? '', $file->parent())?->is($file)) {
                        return $template;
                    }
                }
            }
        }

        return null;
    }
}
```

It only changes files without a template and skips files without a content file, which Kirby's
Panel always creates on upload. Adjust `'video'` if the project's video blueprint has another name.
A file that is a video of its own and also a format of another one keeps the `video` template.

## Low impact changes

### Blueprint names

**Likelihood of impact: Low**

Every blueprint is also registered as `schema/<type>/<name>`, and the plugin's own blueprints use
those names. `@hksagentur/schema/…` references keep working, but they get the original and skip
customizations a project makes in `site/blueprints/schema/` (see the README). Replace them once:

```yaml
# Before
link: '@hksagentur/schema/fields/link'

# After
link: schema/fields/link
```

### Blueprint aliases

**Likelihood of impact: Low**

The `hksagentur.schema.aliases` option is gone. Use the short `schema/…` names instead, or extend a
blueprint from a file of the project's own to use it without a prefix:

```yaml
# site/blueprints/files/image.yml
extends: schema/files/image
```

### Blueprint class

**Likelihood of impact: Low**

`Hks\Schema\Plugin\Blueprint` is gone. `Hks\Schema\Cms\Blueprint::namespace()` registers a directory
of blueprints the way this plugin does; `vendor()` and `package()` split a `@vendor/package`
namespace.

### Formatter and factory classes

**Likelihood of impact: Low**

`AddressFactory`, `AddressFormatter`, `OpeningHoursFactory`, `OpeningHoursFormatter`,
`CurrencyFormatter`, `NumberFormatter`, `GeoCoordinateFactory` and `GeoCoordinateFormatter` are
gone. Use the value objects in `Hks\Schema\Data\Types` (`PostalAddress`, `OpeningHours`, `Money`,
`GeoCoordinate`) through `toDataType()`/`toDataObject()`.

### National phone numbers

**Likelihood of impact: Low**

`PhoneNumber::$nationalSignificantNumber` included the trunk prefix (`04202 91016-0`), which E.164
excludes from the national significant number. The property is now `$nationalNumber`, and
`nationalSignificantNumber()` returns the number without the trunk prefix (`4202 91016-0`).

```php
// Before
$phone->nationalSignificantNumber

// After
$phone->nationalNumber()
```

### Suggested packages

**Likelihood of impact: Low**

The plugin no longer suggests `commerceguys/addressing`, `spatie/opening-hours` or `getkirby/geo`.
Remove them if the project installed them only for this plugin.
