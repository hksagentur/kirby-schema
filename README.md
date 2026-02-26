# Kirby Schema

Frequently used data structures for [Kirby CMS](https://getkirby.com).

## Requirements

Kirby CMS (`>=4.0`)  
PHP (`>= 8.3`)

## Installation

### Composer

```sh
composer require hksagentur/kirby-schema
```

### Download

Download the project archive and copy the files to the plugin directory of your kirby installation. By default this directory is located at `/site/plugins`.

## Usage

All blueprints provided by the plugin are registered within a custom namespace (`@hksagentur/schema`). You have to reference or extend these blueprints to take advantage of the provided data structures.

A simple exampe would be to use the navigation blueprint for the site:

```yaml
# site/blueprints/site.yml
tabs:
  content:
    label: Content
    fields: []
  settings:
    label: Settings
    fields:
      navigation: @hksagentur/schema/fields/navigation
```

Instead of referencing the provided blueprints directly you can use them as a base and start customizing:

```yaml
# site/blueprints/site.yml
tabs:
  content:
    label: Content
    fields: []
  settings:
    label: Settings
    fields:
      navigation:
        extends: @hksagentur/schema/fields/navigation
        fields:
          classNames:
            type: text
            label: HTML Classes
          target: false
          rel: false
```

This applies to all blueprints regardless of their type.

If you only want to use a blueprint without extending it, you can let the plugin generate aliases for you:

```php
<?php

// site/config/config.php
return [
  'hksagentur.schema.aliases' => [
    'files/image',
    'files/video',
  ],
];
```

This would make the image and video blueprints available without the namespace as `files/image` or `files/video`. You can also provide a custom name, if you like:

```php
<?php

// site/config/config.php
return [
  'hksagentur.schema.aliases' => [
    'files/image' => 'files/gallery-image',
    'files/video' => 'files/gallery-video',
  ],
];
```

## Available Blueprints

Below is an overview of all available blueprints provided by this plugin. Each entry lists the readable name, the ID (for reference/extension), and a short description.

### Block Blueprints

| Name                                                  | ID                                      | Description                                                 |
| ----------------------------------------------------- | --------------------------------------- | ----------------------------------------------------------- |
| [Accordion](config/blueprints/blocks/accordion.yml)   | `@hksagentur/schema/blocks/accordion`   | Collapsible group of contents.                              |
| [Billboard](config/blueprints/blocks/billboard.yml)   | `@hksagentur/schema/blocks/billboard`   | Promotional banner showcasing key messages or visuals.      |
| [Cards](config/blueprints/blocks/cards.yml)           | `@hksagentur/schema/blocks/cards`       | Present related content in concise, self-containing blocks. |
| [Collection](config/blueprints/blocks/collection.yml) | `@hksagentur/schema/blocks/collection`  | Customizable list of related pages.                         |
| [Disclosure](config/blueprints/blocks/disclosure.yml) | `@hksagentur/schema/blocks/disclosure`  | Expandable and collapsible content section.                 |
| [Gallery](config/blueprints/blocks/gallery.yml)       | `@hksagentur/schema/blocks/gallery`     | Group related images in a gallery.                          |
| [Heading](config/blueprints/blocks/heading.yml)       | `@hksagentur/schema/blocks/heading`     | Define the documents hierarchy using headings.              |
| [Hero](config/blueprints/blocks/hero.yml)             | `@hksagentur/schema/blocks/hero`        | Prominent section highlighting main message.                |
| [Image](config/blueprints/blocks/image.yml)           | `@hksagentur/schema/blocks/image`       | Embed images from internal or external sources.             |
| [Media Text](config/blueprints/blocks/media-text.yml) | `@hksagentur/schema/blocks/media-text`  | Combine media with descriptive text.                        |
| [Tabs](config/blueprints/blocks/tabs.yml)             | `@hksagentur/schema/blocks/tabs`        | Spread related contents across multiple tabs.               |
| [Video](config/blueprints/blocks/video.yml)           | `@hksagentur/schema/blocks/video`       | Play embedded media content.                                |

### Field Blueprints

| Name                                                              | ID                                           | Description                                         |
| ----------------------------------------------------------------- | -------------------------------------------- | --------------------------------------------------- |
| [Actions](config/blueprints/fields/actions.yml)                   | `@hksagentur/schema/fields/actions`          | Group of links.                                     |
| [Address](config/blueprints/fields/address.yml)                   | `@hksagentur/schema/fields/address`          | Postal address fields.                              |
| [Align Content](config/blueprints/fields/align-content.yml)       | `@hksagentur/schema/fields/align-content`    | Vertical alignment of content items.                |
| [Author](config/blueprints/fields/author.yml)                     | `@hksagentur/schema/fields/author`           | Assign a user as content author.                    |
| [Blocks](config/blueprints/fields/blocks.yml)                     | `@hksagentur/schema/fields/blocks`           | Flexible content builder using Kirby blocks.        |
| [Caption](config/blueprints/fields/caption.yml)                   | `@hksagentur/schema/fields/caption`          | Short description for images or figures.            |
| [Category](config/blueprints/fields/category.yml)                 | `@hksagentur/schema/fields/category`         | Category selection with autocompletion.             |
| [Channels](config/blueprints/fields/channels.yml)                 | `@hksagentur/schema/fields/channels`         | Link to related social media channels.              |
| [Content Layout](config/blueprints/fields/content-layout.yml)     | `@hksagentur/schema/fields/content-layout`   | Arrange content blocks in a layout.                 |
| [Coordinates](config/blueprints/fields/coordinates.yml)           | `@hksagentur/schema/fields/coordinates`      | Geographic coordinates of a place.                  |
| [Cover](config/blueprints/fields/cover.yml)                       | `@hksagentur/schema/fields/cover`            | Preview image of a page or entity.                  |
| [Created](config/blueprints/fields/created.yml)                   | `@hksagentur/schema/fields/created`          | Automatically populated creation date.              |
| [Currency](config/blueprints/fields/currency.yml)                 | `@hksagentur/schema/fields/currency`         | Select the currency for prices or amounts.          |
| [Email](config/blueprints/fields/email.yml)                       | `@hksagentur/schema/fields/email`            | Email address of a person or organization.          |
| [Eyebrow](config/blueprints/fields/eyebrow.yml)                   | `@hksagentur/schema/fields/eyebrow`          | Small text above a headline.                        |
| [Fax](config/blueprints/fields/fax.yml)                           | `@hksagentur/schema/fields/fax`              | Fax number of a person or organization.             |
| [Featured](config/blueprints/fields/featured.yml)                 | `@hksagentur/schema/fields/featured`         | Mark content as featured.                           |
| [Gallery](config/blueprints/fields/gallery.yml)                   | `@hksagentur/schema/fields/gallery`          | Group related images in a gallery.                  |
| [Heading Level](config/blueprints/fields/heading-level.yml)       | `@hksagentur/schema/fields/heading-level`    | HTML heading level to choose from.                  |
| [Heading](config/blueprints/fields/heading.yml)                   | `@hksagentur/schema/fields/heading`          | Text input for headlines or titles.                 |
| [Hours](config/blueprints/fields/opening-hours.yml)               | `@hksagentur/schema/fields/opening-hours`    | Opening hours of a store or local business.         |
| [Image](config/blueprints/fields/image.yml)                       | `@hksagentur/schema/fields/image`            | File picker for image files.                        |
| [Inline Text](config/blueprints/fields/inline-text.yml)           | `@hksagentur/schema/fields/inline-text`      | Formatted inline text elements.                     |
| [Justify Content](config/blueprints/fields/justify-content.yml)   | `@hksagentur/schema/fields/justify-content`  | Horizontal alignment of content items.              |
| [License](config/blueprints/fields/license.yml)                   | `@hksagentur/schema/fields/license`          | A license document applied to an item.              |
| [Limit](config/blueprints/fields/limit.yml)                       | `@hksagentur/schema/fields/limit`            | Specify a maximum value or amount.                  |
| [Link](config/blueprints/fields/link.yml)                         | `@hksagentur/schema/fields/link`             | Reference an internal or external document.         |
| [Logo](config/blueprints/fields/logo.yml)                         | `@hksagentur/schema/fields/logo`             | File picker for a logo.                             |
| [Markdown](config/blueprints/fields/markdown.yml)                 | `@hksagentur/schema/fields/markdown`         | Markdown editor for formatted text.                 |
| [Name](config/blueprints/fields/name.yml)                         | `@hksagentur/schema/fields/name`             | Separate fields for the individual parts of a name. |
| [Navigation](config/blueprints/fields/navigation.yml)             | `@hksagentur/schema/fields/navigation`       | Hierarchical navigation structure.                  |
| [Occupation](config/blueprints/fields/occupation.yml)             | `@hksagentur/schema/fields/occupation`       | Assign one or more occupations to a person.         |
| [Offset](config/blueprints/fields/offset.yml)                     | `@hksagentur/schema/fields/offset`           | Display offset for lists or queries.                |
| [Open](config/blueprints/fields/open.yml)                         | `@hksagentur/schema/fields/open`             | Toggle open/closed state of blocks.                 |
| [Organization](config/blueprints/fields/organization.yml)         | `@hksagentur/schema/fields/organization`     | Organization details with address and contact info. |
| [Person](config/blueprints/fields/person.yml)                     | `@hksagentur/schema/fields/person`           | Contact details for a person.                       |
| [Price](config/blueprints/fields/price.yml)                       | `@hksagentur/schema/fields/price`            | The offer price of a product.                       |
| [Published](config/blueprints/fields/published.yml)               | `@hksagentur/schema/fields/published`        | Date when the page was first published.             |
| [Ratio](config/blueprints/fields/ratio.yml)                       | `@hksagentur/schema/fields/ratio`            | Selection of common aspect ratios.                  |
| [Responsibilities](config/blueprints/fields/responsibilities.yml) | `@hksagentur/schema/fields/responsibilities` | Assign one or more responsibilities to a person.    |
| [Reverse](config/blueprints/fields/reverse.yml)                   | `@hksagentur/schema/fields/reverse`          | Reverse the order of a sequence.                    |
| [Rich Text](config/blueprints/fields/rich-text.yml)               | `@hksagentur/schema/fields/rich-text`        | Editor for formatted, rich content.                 |
| [Source](config/blueprints/fields/source.yml)                     | `@hksagentur/schema/fields/source`           | Source and license information of a file.           |
| [Tags](config/blueprints/fields/tags.yml)                         | `@hksagentur/schema/fields/tags`             | Associate an entity with tags.                      |
| [Telephone](config/blueprints/fields/telephone.yml)               | `@hksagentur/schema/fields/telephone`        | Telephone number of a person or organization.       |
| [Updated](config/blueprints/fields/updated.yml)                   | `@hksagentur/schema/fields/updated`          | Last modification date of a page.                   |
| [Video](config/blueprints/fields/video.yml)                       | `@hksagentur/schema/fields/video`            | File picker for videos files.                       |
| [Website](config/blueprints/fields/website.yml)                   | `@hksagentur/schema/fields/website`          | URL to an external website.                         |

### File Blueprints

| Name                                             | ID                                  | Description                                                  |
| ------------------------------------------------ | ----------------------------------- | ------------------------------------------------------------ |
| [Document](config/blueprints/files/document.yml) | `@hksagentur/schema/files/document` | Complementary documents such as PDFs or compressed archives. |
| [Image](config/blueprints/files/image.yml)       | `@hksagentur/schema/files/image`    | Generic image with alternative text and caption.             |
| [Logo](config/blueprints/files/logo.yml)         | `@hksagentur/schema/files/logo`     | Visual identity of a brand or institution.                   |
| [Video](config/blueprints/files/video.yml)       | `@hksagentur/schema/files/video`    | Video file with poster and caption.                          |

### Page Blueprints

| Name                                             | ID                                  | Description                                           |
| ------------------------------------------------ | ----------------------------------- | ----------------------------------------------------- |
| [Article](config/blueprints/pages/article.yml)   | `@hksagentur/schema/pages/article`  | Short publication like a news article or blog post.   |
| [Page](config/blueprints/pages/default.yml)      | `@hksagentur/schema/pages/default`  | Generic page using the Kirby block builder.           |
| [Employee](config/blueprints/pages/employee.yml) | `@hksagentur/schema/pages/employee` | Personal profile of an employee.                      |

## License

ISC License. Please see [License File](LICENSE.txt) for more information.