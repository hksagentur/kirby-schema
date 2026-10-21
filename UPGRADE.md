# Upgrade Guide

## Upgrading to 2.0 from 1.x

v2 is a rewrite and not a drop-in upgrade. Changes that touch stored content need a
[content migration](#content-migrations); everything else is an update to blueprints or PHP code.

### High impact changes

- [Updating dependencies](#updating-dependencies)
- [Address fields](#address-fields)
- [Link target](#link-target)
- [Person tags](#person-tags)
- [Model traits](#model-traits)
- [Responsive images and videos](#responsive-images-and-videos)

### Medium impact changes

- [Opening hours fields](#opening-hours-fields)
- [Field methods](#field-methods)
- [Links of `HasLink` models](#links-of-haslink-models)
- [Block accessors](#block-accessors)
- [Image and video options](#image-and-video-options)

### Low impact changes

- [Blueprint aliases](#blueprint-aliases)
- [Blueprint class](#blueprint-class)
- [Formatter and factory classes](#formatter-and-factory-classes)
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
  videos — v1 installed it, v2 no longer does

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
| `Hks\Schema\Cms\HasBlockContent` | `Concerns\HasBlocks` + `Concerns\HasKeyVisual`        |
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
use Hks\Schema\Cms\Concerns\HasBlocks;
use Hks\Schema\Cms\Concerns\HasKeyVisual;

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

## Medium impact changes

### Opening hours fields

**Likelihood of impact: Medium**

| v1 field ID                                 | v2 field ID                                              |
| ------------------------------------------- | -------------------------------------------------------- |
| `@hksagentur/schema/fields/hours`           | `@hksagentur/schema/fields/opening-hours`                |
| `@hksagentur/schema/fields/hours-day`       | `@hksagentur/schema/fields/opening-hours-specification`  |
| `@hksagentur/schema/fields/hours-exception` | `@hksagentur/schema/fields/opening-hours-exception`      |

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

`ratio()` on `GalleryBlock`, `ImageBlock` and `VideoBlock` no longer falls back to `'auto'`, and
`ImageBlock::thumb()`, `thumbOptions()`, `thumbDimensions()` and `thumbResizeStrategy()` are gone.

```php
// Before
$block->ratio()->value()

// After
$block->ratio()->or('auto')->value()
```

### Image and video options

**Likelihood of impact: Medium**

`hksagentur.schema.image` and `hksagentur.schema.video` are no longer read; configure
`hksagentur.media-kit.image`/`.video` instead. v1 merged its options *under* media-kit's defaults,
so a `hksagentur.schema.image.formats` setting never took effect — check the rendered output before
moving it over.

## Low impact changes

### Blueprint aliases

**Likelihood of impact: Low**

The `hksagentur.schema.aliases` option is gone. To use a blueprint without the namespace, extend it
from a file of the project's own:

```yaml
# site/blueprints/files/image.yml
extends: @hksagentur/schema/files/image
```

### Blueprint class

**Likelihood of impact: Low**

`Hks\Schema\Plugin\Blueprint` moved to `Hks\Schema\Cms\Blueprint`.

### Formatter and factory classes

**Likelihood of impact: Low**

`AddressFactory`, `AddressFormatter`, `OpeningHoursFactory`, `OpeningHoursFormatter`,
`CurrencyFormatter`, `NumberFormatter`, `GeoCoordinateFactory` and `GeoCoordinateFormatter` are
gone. Use the value objects in `Hks\Schema\Data\Types` (`PostalAddress`, `OpeningHours`, `Money`,
`GeoCoordinate`) through `toDataType()`/`toDataObject()`.

### Suggested packages

**Likelihood of impact: Low**

The plugin no longer suggests `commerceguys/addressing`, `spatie/opening-hours` or `getkirby/geo`.
Remove them if the project installed them only for this plugin.
