# Migrating from v1 to v2

v2 is a from-scratch rewrite (see [ROADMAP.md](ROADMAP.md)) and is **not** a drop-in upgrade. There
is no automated migration script — this is a plain list of what changed and what to do about it in a
project that already uses v1 blueprints, field methods, or file methods.

## Requirements

- Kirby CMS `^5.1` (was `>=4.0`).
- PHP `>=8.3` (unchanged).
- `hksagentur/kirby-media-kit` is no longer an optional dependency of any kind — v1's `suggest` block
  (`commerceguys/addressing`, `spatie/opening-hours`, `getkirby/geo`) is gone. None of those packages
  are used or required by v2; if a project installed them only for this plugin, they can be removed.

## Renamed blueprint fields

| v1 field ID                                  | v2 field ID                                                    |
| --------------------------------------------- | ---------------------------------------------------------------- |
| `@hksagentur/schema/fields/hours`             | `@hksagentur/schema/fields/opening-hours`                        |
| `@hksagentur/schema/fields/hours-day`         | `@hksagentur/schema/fields/opening-hours-specification`          |
| `@hksagentur/schema/fields/hours-exception`   | `@hksagentur/schema/fields/opening-hours-exception`              |

The stored content *shape* is unchanged, so this is a blueprint-reference update, not a content
migration: each weekday row still stores `start`/`end` directly (e.g. `monday: [{start: "09:00", end:
"19:00"}]`), and `exceptions` rows still store `date`/`start`/`end`/`note` directly — `time-range`
(used by `opening-hours-specification`'s weekday rows) is a `group` field that flattens fully into
its containing structure row rather than nesting under a field of its own name, the same as in v1.
Update the `extends:`/reference in a project's own blueprint to the new field IDs above;
`OpeningHours::fromArray()` (used by `toDataType('opening-hours')`) reads the existing rows as-is. The
only thing that changes stored content is if a project also renames the *top-level* field itself (the
key a project chose in its own blueprint, e.g. `hours: @hksagentur/schema/fields/hours`) to match the
new ID (`openingHours: @hksagentur/schema/fields/opening-hours`) — that rename is optional and, if
made, needs the corresponding top-level content key renamed too.

## New blueprint field

- `@hksagentur/schema/fields/place` — name + address + coordinates, backed by the new
  [`Place`](src/Data/Place.php) `DataObject`. No v1 equivalent; adopt it where a project previously
  hand-rolled a similar name/address/coordinates group.
- `@hksagentur/schema/fields/currency` and `@hksagentur/schema/fields/price` now have a backing value
  object ([`Money`](src/Data/Types/Money.php)/[`Currency`](src/Data/Types/Currency.php)) where in v1
  they were formatted ad hoc via `CurrencyFormatter`/`NumberFormatter`.
- `@hksagentur/schema/fields/link` (and `actions`, a `structure` of `link` rows) now resolves to a
  [`Link`](src/Data/Link.php) `DataObject` via `toDataObject('link')` — `url()`/`page()`/`file()`/
  `user()`/`model()`, an `isActive()`/`isOpen()` state for internal page links, and `toHtml()`
  rendering through the overridable `schema/link` snippet. No v1 equivalent (the field existed, but
  had no backing value object).

## Changed blueprint field: `link.yml`'s `target`

**Breaking, requires a content migration** — `target` was a `type: toggle` (a boolean: blank or not)
in v1; it's now `type: toggles` with four options (`_self`/`_blank`/`_parent`/`_top`, mirroring the
HTML `target` attribute), matching what a link can actually do instead of a binary "new window?"
choice. Existing v1 content stores `target: true`/`target: false`/no value at all — none of those
match the new options, and resolving one through `Link::fromArray()` (via `toDataObject('link')`)
throws a `TypeError` for a `true`/`false` value (only a *missing* key falls back to the new default,
`_self`). A project with existing `link`/`actions` content needs a one-time content migration:
`target: true` → `target: _blank`, `target: false` or absent → `target: _self` (or delete the key
entirely, since `_self` is the default).

## Changed blueprint field: `person.yml`'s occupation/responsibilities

v1 had two flat `tags` fields directly on `person.yml`: `occupation` (max 3) and `responsibilities`
(max 3) — named this way due to a translation slip by a non-native English speaker, not because
they matched their actual meaning ("what did this person study" and "what are they responsible for
in the company"). v2 replaces both with three properly-named, schema.org-mapped fields:

| v1 field                                    | v2 field                                          |
| -------------------------------------------- | --------------------------------------------------- |
| *(none — job title wasn't captured at all)* | `jobTitle` (single `text` field)                    |
| `occupation` (tags, max 3)                  | `qualifications` (`@hksagentur/schema/fields/qualifications`, an `entries` list) |
| `responsibilities` (tags, max 3)            | `expertise` (`@hksagentur/schema/fields/expertise`, an `entries` list)           |

There is no content-shape compatibility between v1's `occupation`/`responsibilities` and v2's
`qualifications`/`expertise` — both changed field type (`tags` → `entries`) and field name, so this
needs a manual content migration (rename the keys, and re-enter the values as an `entries` list) for
any project carrying v1 content here. `Person::$jobTitle`/`qualifications()`/`expertise()` map to
schema.org's `jobTitle`/`hasCredential`/`knowsAbout` respectively in `toStructuredData()`.

## Dropped field methods

These `Field` methods from v1 are gone and have no direct replacement method — use
`$field->toDataType('name')` / `$field->toDataObject('name')` instead (see the README's "Converting
Content to Value Objects" section) and call `toHtml()`/`toStructuredData()`/`toArray()` on the
resulting value object:

- `toIcon()`
- `toStructuredLink()`
- `toCurrency()` — replace with `$field->toDataType('money')`.
- `toNumber()`
- `toDecimal()`
- `toOpeningHours()` — replace with `$field->toDataType('opening-hours')`.

## Dropped file methods

- `toResponsiveImage()`
- `toResponsiveVideo()`

Responsive image/video rendering (srcset, sizes, presets) moved to the separate
[`hksagentur/kirby-media-kit`](https://github.com/hksagentur/kirby-media-kit) package and is no
longer part of kirby-schema at all (see [AGENTS.md](AGENTS.md) "Media handling is out of scope
here"). A project that used these methods needs to install `kirby-media-kit` and switch to its
`PendingImage`/`PendingFile` API.

## Moved class

- `Hks\Schema\Plugin\Blueprint` → `Hks\Schema\Cms\Blueprint`. Update any direct references (a project
  extending or type-hinting the class directly — most won't, since it's normally only used internally
  by the plugin's own blueprint registration).

## Formatter/factory classes removed entirely

v1's `AddressFactory`, `AddressFormatter`, `OpeningHoursFactory`, `OpeningHoursFormatter`,
`CurrencyFormatter`, `NumberFormatter`, and `GeoCoordinateFactory`/`GeoCoordinateFormatter` are gone.
Anything a project built directly against these classes needs to move to the corresponding
`Hks\Schema\Data\Types\*` value object (`PostalAddress`, `OpeningHours`, `Money`, `GeoCoordinate`)
reached via `toDataType()`/`toDataObject()`.
