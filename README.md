# Kirby Schema

Frequently used data structures for [Kirby CMS](https://getkirby.com).

## Requirements

Kirby CMS (`^5.1`)  
PHP (`>= 8.3`)

## Installation

### Composer

```sh
composer require hksagentur/kirby-schema
```

### Download

Download the project archive and copy the files to the plugin directory of your kirby installation. By default this directory is located at `/site/plugins`.

## Usage

Every blueprint of the plugin is registered under two names: `schema/<type>/<name>` to use and
customize, and `@hksagentur/schema/<type>/<name>` for the original. Reference a blueprint by its
`schema/` name, e.g. the navigation for the site:

```yaml
# site/blueprints/site.yml
tabs:
  settings:
    label: Settings
    fields:
      navigation: schema/fields/navigation
```

Extend it where it's used to change it in one place:

```yaml
navigation:
  extends: schema/fields/navigation
  fields:
    classNames:
      type: text
      label: HTML Classes
    rel: false
```

### Customizing blueprints

The plugin's blocks, pages and tabs use the `schema/` names as well. To change a blueprint
everywhere — e.g. to add a field from another plugin — put a file of the same name into
`site/blueprints/schema/` (or register the name in a plugin) and extend the original:

```yaml
# site/blueprints/schema/fields/link.yml
extends: '@hksagentur/schema/fields/link'
fields:
  icon:
    type: icon
```

Leave out `extends` to replace the blueprint entirely. This works like overriding one of Kirby's
core blocks (see [Customizing core blocks](https://getkirby.com/docs/guide/page-builder/customizing-core-blocks)),
except that the original stays available to extend instead of being copied. Keep in mind:

- An override applies wherever the `schema/` name is used. A blueprint referencing
  `@hksagentur/schema/…` gets the original.
- Extend the `@hksagentur/schema/` original in an override, never its own `schema/` name: Kirby
  follows `extends` until PHP runs out of memory.
- `site/blueprints/` always wins. A plugin registering a `schema/` name only wins if it loads after
  this plugin — Kirby loads plugins in alphabetical order.

## Value Objects

Content stored by the plugin's blueprints converts into immutable value objects that render their
own HTML (with microdata and microformats) and schema.org structured data:

```php
<?php // site/templates/employee.php ?>

<?php $person = $page->toDataObject('person') ?>
<?= $person->toHtml() ?>

<script type="application/ld+json">
  <?= json_encode(['@context' => 'https://schema.org', ...$person->toStructuredData()]) ?>
</script>
```

Use `$field->toDataType()` for a single field (e.g. `'opening-hours'`, `'money'`, `'phone-number'`)
and `toDataObject()` for content made of several fields (`'person'`, `'organization'`, `'place'`,
`'contact-point'`, `'link'`) on a page, structure row or field. The HTML comes from snippets named `schema/{type}`,
which a project overrides by placing its own file at `site/snippets/schema/{type}.php`.

## Upgrading

See [UPGRADE.md](UPGRADE.md) for upgrading from v1, and [CHANGELOG.md](CHANGELOG.md) for all
changes.

## Available Blueprints

Below is an overview of all available blueprints provided by this plugin. Each entry lists the readable name, the ID (for reference/extension; the original is the same ID prefixed with `@hksagentur/`), and a short description.

### Block Blueprints

| Name                                                  | ID                                      | Description                                                 |
| ----------------------------------------------------- | --------------------------------------- | ----------------------------------------------------------- |
| [Accordion](config/blueprints/blocks/accordion.yml)   | `schema/blocks/accordion`   | Collapsible group of contents.                              |
| [Billboard](config/blueprints/blocks/billboard.yml)   | `schema/blocks/billboard`   | Promotional banner showcasing key messages or visuals.      |
| [Cards](config/blueprints/blocks/cards.yml)           | `schema/blocks/cards`       | Present related content in concise, self-containing blocks. |
| [Collection](config/blueprints/blocks/collection.yml) | `schema/blocks/collection`  | Customizable list of related pages.                         |
| [Disclosure](config/blueprints/blocks/disclosure.yml) | `schema/blocks/disclosure`  | Expandable and collapsible content section.                 |
| [Gallery](config/blueprints/blocks/gallery.yml)       | `schema/blocks/gallery`     | Group related images in a gallery.                          |
| [Heading](config/blueprints/blocks/heading.yml)       | `schema/blocks/heading`     | Define the documents hierarchy using headings.              |
| [Hero](config/blueprints/blocks/hero.yml)             | `schema/blocks/hero`        | Prominent section highlighting main message.                |
| [Image](config/blueprints/blocks/image.yml)           | `schema/blocks/image`       | Embed images from internal or external sources.             |
| [Media Text](config/blueprints/blocks/media-text.yml) | `schema/blocks/media-text`  | Combine media with descriptive text.                        |
| [Tabs](config/blueprints/blocks/tabs.yml)             | `schema/blocks/tabs`        | Spread related contents across multiple tabs.               |
| [Video](config/blueprints/blocks/video.yml)           | `schema/blocks/video`       | Play embedded media content.                                |

### Field Blueprints

| Name                                                              | ID                                           | Description                                         |
| ----------------------------------------------------------------- | -------------------------------------------- | --------------------------------------------------- |
| [Actions](config/blueprints/fields/actions.yml)                   | `schema/fields/actions`          | Group of links.                                     |
| [Address](config/blueprints/fields/address.yml)                   | `schema/fields/address`          | Postal address fields.                              |
| [Align Content](config/blueprints/fields/align-content.yml)       | `schema/fields/align-content`    | Vertical alignment of content items.                |
| [Author](config/blueprints/fields/author.yml)                     | `schema/fields/author`           | Assign a user as content author.                    |
| [Blocks](config/blueprints/fields/blocks.yml)                     | `schema/fields/blocks`           | Flexible content builder using Kirby blocks.        |
| [Caption](config/blueprints/fields/caption.yml)                   | `schema/fields/caption`          | Short description for images or figures.            |
| [Category](config/blueprints/fields/category.yml)                 | `schema/fields/category`         | Category selection with autocompletion.             |
| [Channels](config/blueprints/fields/channels.yml)                 | `schema/fields/channels`         | Link to related social media channels.              |
| [Content Layout](config/blueprints/fields/content-layout.yml)     | `schema/fields/content-layout`   | Arrange content blocks in a layout.                 |
| [Contact point](config/blueprints/fields/contact-point.yml)      | `schema/fields/contact-point`    | Contact type, telephone and email of an organization. |
| [Contact points](config/blueprints/fields/contact-points.yml)    | `schema/fields/contact-points`   | Structure of contact points.                        |
| [Coordinates](config/blueprints/fields/coordinates.yml)           | `schema/fields/coordinates`      | Geographic coordinates of a place.                  |
| [Cover](config/blueprints/fields/cover.yml)                       | `schema/fields/cover`            | Preview image of a page or entity.                  |
| [Created](config/blueprints/fields/created.yml)                   | `schema/fields/created`          | Automatically populated creation date.              |
| [Currency](config/blueprints/fields/currency.yml)                 | `schema/fields/currency`         | Select the currency for prices or amounts.          |
| [Email](config/blueprints/fields/email.yml)                       | `schema/fields/email`            | Email address of a person or organization.          |
| [Expertise](config/blueprints/fields/expertise.yml)               | `schema/fields/expertise`        | Subject-matter areas a person is knowledgeable in.  |
| [Eyebrow](config/blueprints/fields/eyebrow.yml)                   | `schema/fields/eyebrow`          | Small text above a headline.                        |
| [Fax](config/blueprints/fields/fax.yml)                           | `schema/fields/fax`              | Fax number of a person or organization.             |
| [Featured](config/blueprints/fields/featured.yml)                 | `schema/fields/featured`         | Mark content as featured.                           |
| [Gallery](config/blueprints/fields/gallery.yml)                   | `schema/fields/gallery`          | Group related images in a gallery.                  |
| [Heading Level](config/blueprints/fields/heading-level.yml)       | `schema/fields/heading-level`    | HTML heading level to choose from.                  |
| [Heading](config/blueprints/fields/heading.yml)                   | `schema/fields/heading`          | Text input for headlines or titles.                 |
| [Opening Hours](config/blueprints/fields/opening-hours.yml)       | `schema/fields/opening-hours`    | Opening hours of a store or local business.         |
| [Image](config/blueprints/fields/image.yml)                       | `schema/fields/image`            | File picker for image files.                        |
| [Inline Text](config/blueprints/fields/inline-text.yml)           | `schema/fields/inline-text`      | Formatted inline text elements.                     |
| [Justify Content](config/blueprints/fields/justify-content.yml)   | `schema/fields/justify-content`  | Horizontal alignment of content items.              |
| [License](config/blueprints/fields/license.yml)                   | `schema/fields/license`          | A license document applied to an item.              |
| [Limit](config/blueprints/fields/limit.yml)                       | `schema/fields/limit`            | Specify a maximum value or amount.                  |
| [Link](config/blueprints/fields/link.yml)                         | `schema/fields/link`             | Reference an internal or external document.         |
| [Logo](config/blueprints/fields/logo.yml)                         | `schema/fields/logo`             | File picker for a logo.                             |
| [Markdown](config/blueprints/fields/markdown.yml)                 | `schema/fields/markdown`         | Markdown editor for formatted text.                 |
| [Name](config/blueprints/fields/name.yml)                         | `schema/fields/name`             | Separate fields for the individual parts of a name. |
| [Navigation](config/blueprints/fields/navigation.yml)             | `schema/fields/navigation`       | Hierarchical navigation structure.                  |
| [Offset](config/blueprints/fields/offset.yml)                     | `schema/fields/offset`           | Display offset for lists or queries.                |
| [Open](config/blueprints/fields/open.yml)                         | `schema/fields/open`             | Toggle open/closed state of blocks.                 |
| [Organization](config/blueprints/fields/organization.yml)         | `schema/fields/organization`     | Organization details with address and contact info. |
| [Person](config/blueprints/fields/person.yml)                     | `schema/fields/person`           | Contact details for a person.                       |
| [Place](config/blueprints/fields/place.yml)                       | `schema/fields/place`            | Name and address of a physical location.            |
| [Price](config/blueprints/fields/price.yml)                       | `schema/fields/price`            | The offer price of a product.                       |
| [Published](config/blueprints/fields/published.yml)               | `schema/fields/published`        | Date when the page was first published.             |
| [Qualifications](config/blueprints/fields/qualifications.yml)     | `schema/fields/qualifications`   | Academic degrees or professional qualifications.    |
| [Ratio](config/blueprints/fields/ratio.yml)                       | `schema/fields/ratio`            | Selection of common aspect ratios.                  |
| [Reverse](config/blueprints/fields/reverse.yml)                   | `schema/fields/reverse`          | Reverse the order of a sequence.                    |
| [Rich Text](config/blueprints/fields/rich-text.yml)               | `schema/fields/rich-text`        | Editor for formatted, rich content.                 |
| [Source](config/blueprints/fields/source.yml)                     | `schema/fields/source`           | Source and license information of a file.           |
| [Tags](config/blueprints/fields/tags.yml)                         | `schema/fields/tags`             | Associate an entity with tags.                      |
| [Telephone](config/blueprints/fields/telephone.yml)               | `schema/fields/telephone`        | Telephone number of a person or organization.       |
| [Updated](config/blueprints/fields/updated.yml)                   | `schema/fields/updated`          | Last modification date of a page.                   |
| [Video](config/blueprints/fields/video.yml)                       | `schema/fields/video`            | File picker for videos files.                       |
| [Website](config/blueprints/fields/website.yml)                   | `schema/fields/website`          | URL to an external website.                         |

### File Blueprints

| Name                                             | ID                                  | Description                                                  |
| ------------------------------------------------ | ----------------------------------- | ------------------------------------------------------------ |
| [Document](config/blueprints/files/document.yml) | `schema/files/document` | Complementary documents such as PDFs or compressed archives. |
| [Image](config/blueprints/files/image.yml)       | `schema/files/image`    | Generic image with alternative text and caption.             |
| [Logo](config/blueprints/files/logo.yml)         | `schema/files/logo`     | Visual identity of a brand or institution.                   |
| [Video](config/blueprints/files/video.yml)       | `schema/files/video`    | Video file with poster and caption.                          |

### Page Blueprints

| Name                                             | ID                                  | Description                                           |
| ------------------------------------------------ | ----------------------------------- | ----------------------------------------------------- |
| [Article](config/blueprints/pages/article.yml)   | `schema/pages/article`  | Short publication like a news article or blog post.   |
| [Page](config/blueprints/pages/default.yml)      | `schema/pages/default`  | Generic page using the Kirby block builder.           |
| [Employee](config/blueprints/pages/employee.yml) | `schema/pages/employee` | Personal profile of an employee.                      |

## License

ISC License. Please see [License File](LICENSE.txt) for more information.