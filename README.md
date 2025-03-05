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

## License

ISC License. Please see [License File](LICENSE.txt) for more information.