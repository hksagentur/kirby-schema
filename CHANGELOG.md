# Changelog

All notable changes to this project are documented in this file.

## 2.0.0-alpha.2 — 2026-10-22

### Added

- `Hks\Schema\Migration\Migration` and the fluent, immutable `Migration\Changeset` for content
  migrations, and the `kirby schema:migrate [--dry-run]` command running those a project registers
  in the new `hksagentur.schema.migrations` option. UPGRADE.md has migrations for the v1 → v2
  changes.
- `Link::icon()` — an optional icon read from an `icon` key a project adds to `link.yml`; the
  shipped `schema/link` snippet doesn't render it.
- `Cms\Concerns\HasImage`/`HasVideo` for models with a single `image`/`video` field;
  `HasMediaFile` composes both.
- `Hks\Schema\Format\PostalAddressFormat` and `PhoneNumberFormat`, extending `Format\Format`
  (`get()`/`default()`/`all()`, plus `PhoneNumberFormat::byCallingCode()`), reading the shipped
  `resources/address-formats.php` and `resources/calling-codes.php`. Projects add or override
  countries via the `addressFormats` and the new `phoneNumberFormats` option.
- `Hks\Schema\Toolkit\Str::squish()` collapses whitespace to single spaces and trims whitespace and
  invisible format characters (zero-width space, BOM, …) from the ends.
- `Money::isSameCurrency()` checks whether two amounts share a currency.

### Changed

- `toOxfordList()` is renamed to `oxfordList($method, $limit, $locale)`: it returns a `Field`, so
  it follows Kirby's manipulators (`excerpt()`, `short()`) rather than the `to*` converters. The new
  leading `$method` reads the value like Kirby's `toData()` (a separator, `yaml` or `json`), so
  `entries` fields such as `qualifications` format via `oxfordList('yaml')`. Calls from alpha.1
  need the new name, and a positional limit (`toOxfordList(2)`) moves to the second argument.
- The `hksagentur.schema.addressSchemes` option is gone: the templates ship in
  `resources/address-schemes.php`. `hksagentur.schema.addressFormats` keeps its shape (a template
  name or a list of lines per country) but only needs the countries a project adds or overrides.
  `PostalAddress::defaultCountry()`/`addressFormat()`/`addressSchemes()`/`addressFormats()`/
  `addressScheme()`/`addressFormatLines()` and the `DEFAULT_COUNTRY`/`DEFAULT_SCHEME` constants are
  gone; use `PostalAddressFormat::get($code)` / `PostalAddressFormat::default()` and their `lines()`.
  A country listed in the data keeps its own format instead of falling back to the default
  country's.
- Address formats follow Google's address data: `localityPostalCode` is a single line now (Malta,
  New Zealand) and the former two-line variant is `localityPostalCodeMultiline` (UK);
  `localityRegionPostalCode` drops its comma (Australia, Canada) while the US keeps it; Estonia,
  Spain, Italy and Lithuania show the region after the locality (`postalCodeLocalityRegion`);
  Hungary, Latvia, Romania and Turkey get their own formats.
- `Currency` is a `StringType` instead of an enum of six currencies and accepts any three-letter
  ISO 4217 code; symbol and minor units come from `NumberFormatter`, the name from the
  `hksagentur.schema.currency.{CODE}` translations (now keyed by the upper-case code, like country
  names), falling back to the code. Replace `Currency::EUR` with `Currency::from('EUR')`,
  `of()`/`fromName()` with `tryFrom()`, `name()` with `code()` and `label()` with
  `toLocaleString()`; compare currencies with `equals()`. Registered as the `currency` data type
  with a `schema/currency` snippet.
- Defaults come from the `defaultCountry`/`defaultCurrency` options only: `Currency::default()`
  drops its `$fallback` argument and throws for an unsupported currency, and a country without its
  own address format uses the default country's instead of a hard-coded `postalCodeLocality`.
- `Cms\Concerns\HasLink::link()` returns a `?Link` data object instead of a raw `Content` object;
  `target()` resolves through `Link::model()`.
- The `schema/organization` snippet renders `itemtype="https://schema.org/Organization"` (was
  `LocalBusiness`), matching `Organization::toStructuredData()`'s default. Pass a more specific type
  via `toHtml(['itemtype' => …])` and `toStructuredData('LocalBusiness')`.

### Removed

- The `blueprintAliases` option (`aliases` in v1): extend a blueprint from a file of the project's
  own to use it without the namespace.

### Fixed

- `PhoneNumber::toE164String()` converts national numbers using the default (or a given) country
  instead of returning the national digits, and strips the trunk prefix of international numbers
  written as `+49 (0) 221 …` without dropping a parenthesized area code; `toE123String()` drops
  that trunk prefix too. The `schema/phone-number` snippet links to the E.164 number.
- Address lines drop separators and gaps left by an empty part (`Springfield,  12345` →
  `Springfield, 12345`, `34433 Beyoğlu/` → `34433 Beyoğlu`); Kirby's `Str::trim()` only removed a
  repeated `", "` sequence, not single characters.
- `PostalAddress::toStructuredData()` no longer emits empty members (`"addressRegion": null`), and
  `Organization::toStructuredData()` no longer emits an empty `location` place.

## 2.0.0-alpha.1 — 2026-09-10

A from-scratch rewrite. See [UPGRADE.md](UPGRADE.md) for the full list of breaking changes and
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
  `type: toggles` with four options (`_self`/`_blank`/`_parent`/`_top`) — see UPGRADE.md, existing
  content needs a one-time value migration.
- `@hksagentur/schema/fields/person`'s `occupation`/`responsibilities` `tags` fields replaced by
  `jobTitle`/`qualifications`/`expertise` — see UPGRADE.md, no content-shape compatibility.

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

See [UPGRADE.md](UPGRADE.md) before upgrading an existing v1 project.
