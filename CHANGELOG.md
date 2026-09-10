# Changelog

All notable changes to this project are documented in this file.

## 2.0.0-alpha.1 — 2026-09-10

A from-scratch rewrite. See [MIGRATING.md](MIGRATING.md) for the full list of breaking changes and
[ROADMAP.md](ROADMAP.md) for the design rationale. Highlights:

### Added

- Immutable value objects under `Hks\Schema\Data\Types` (`PostalAddress`, `OpeningHours`,
  `PhoneNumber`, `GeoCoordinate`, `PersonName`, `Money`, `Currency`, `Fraction`, `Length`, `Time`,
  `Url`, …) and composite `Hks\Schema\Data\DataObject`s (`Person`, `Organization`, `Place`) — each
  constructed via `from()`/`tryFrom()`/`fromArray()` and able to render its own HTML (`Htmlable`) and
  schema.org/JSON-LD structured data (`Schemable`).
- `$field->toDataType('name')`, `$field->toDataObject('name')`, `$structureObject->toDataObject('name')`,
  and `$page->toDataObject('name')` field/page/structure-object methods that resolve blueprint content
  into those value objects, backed by a new `hksagentur.schema.dataTypes`/`dataObjects` registry
  (`Hks\Schema\Data\Registry`).
- `Hks\Schema\Data\Contracts\*` interfaces (`Arrayable`, `Htmlable`, `Localizable`, `Schemable`,
  `Jsonable`, `Comparable`, `Enumerable`) and `Hks\Schema\Data\Collections\*` typed collections
  (`WeekDays`, `TimeRanges`).
- New `@hksagentur/schema/fields/place` blueprint field, backed by the new `Place` data object.
- `Money`/`Currency` value objects backing `currency.yml`/`price.yml`, with locale-aware formatting.
- `Hks\Schema\Cms\*` traits/contracts for Kirby page/file/block models (`HasLink`, `HasKeyVisual`,
  `HasMediaFile`, `HasExcerpt`, `HasBlocks`, …) and the `Cms\Blocks` classes.
- `Link` data object backing `@hksagentur/schema/fields/link`/`actions` — `url()`/`page()`/`file()`/
  `user()`/`model()` resolution of the field's raw page/file/user/url/email/tel/anchor value, an
  `isActive()`/`isOpen()` state for internal page links, and a new `LinkTarget` enum
  (`_self`/`_blank`/`_parent`/`_top`).
- `Person::$jobTitle`/`qualifications`/`expertise` (new `qualifications.yml`/`expertise.yml`
  blueprint fields), mapped to schema.org's `jobTitle`/`hasCredential`/`knowsAbout` in
  `toStructuredData()`.

### Changed

- `@hksagentur/schema/fields/hours`/`hours-day`/`hours-exception` renamed to `opening-hours`/
  `opening-hours-specification`/`opening-hours-exception`; the stored content shape is unchanged
  (weekday rows still store `start`/`end` directly).
- `Hks\Schema\Plugin\Blueprint` moved to `Hks\Schema\Cms\Blueprint`.
- Minimum requirements raised to Kirby CMS `^5.1` and PHP `>=8.3`.
- `@hksagentur/schema/fields/link`'s `target` changed from a `type: toggle` (boolean) to
  `type: toggles` with four options (`_self`/`_blank`/`_parent`/`_top`) — see MIGRATING.md, existing
  content needs a one-time value migration.
- `@hksagentur/schema/fields/person`'s `occupation`/`responsibilities` `tags` fields replaced by
  `jobTitle`/`qualifications`/`expertise` — see MIGRATING.md, no content-shape compatibility.

### Removed

- v1's `*Factory`/`*Formatter` classes (`AddressFactory`, `AddressFormatter`, `OpeningHoursFactory`,
  `OpeningHoursFormatter`, `CurrencyFormatter`, `NumberFormatter`, `GeoCoordinateFactory`/
  `GeoCoordinateFormatter`) — superseded by `Data\Types`/`Data\*` value objects.
- Field methods `toIcon()`, `toStructuredLink()`, `toCurrency()`, `toNumber()`, `toDecimal()`,
  `toOpeningHours()` — superseded by `toDataType()`/`toDataObject()`.
- File methods `toResponsiveImage()`/`toResponsiveVideo()` — responsive image/video rendering moved
  to the separate [`hksagentur/kirby-media-kit`](https://github.com/hksagentur/kirby-media-kit)
  package and is no longer part of kirby-schema.
- The `suggest` block in `composer.json` (`commerceguys/addressing`, `spatie/opening-hours`,
  `getkirby/geo`) — none of those packages are used by v2.
- `@hksagentur/schema/fields/organization-roles`/`responsibilities` and the `Role` data object —
  never wired to a `Person` property or the `dataObjects` registry in the first place; superseded by
  `Person::$jobTitle`/`qualifications`/`expertise`.

See [MIGRATING.md](MIGRATING.md) before upgrading an existing v1 project.
