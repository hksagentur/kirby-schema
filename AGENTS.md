# AGENTS.md

Guidance for humans and AI coding agents working in this repo.

## What this is

`kirby-schema` is a [Kirby CMS](https://getkirby.com) plugin that ships ready-made blueprints for
frequently repeated data structures (addresses, opening hours, people, organizations, geo
coordinates, phone numbers, …), plus the field/collection/structure-object methods and blocks that
go with them. Projects reference or extend the plugin's blueprints under the
`@hksagentur/schema/...` namespace instead of hand-rolling the same fields per project — see the
[README](README.md) for the extension pattern.

This is `release/2.0.0`, an in-progress major rewrite (see [ROADMAP.md](ROADMAP.md) and
[TODO.md](TODO.md) before starting work — they track the current direction and known gaps).

## v2 direction: blueprints → Value Objects → HTML/JSON-LD

The core idea of v2: blueprint content should convert itself into markup and structured data,
instead of every project writing its own snippet to format an address or render opening hours.

- `src/Data/Types/*` — immutable Value Objects (`readonly class ... extends DataType`) for each
  data shape (`PostalAddress`, `OpeningHours`, `PhoneNumber`, `GeoCoordinate`, `PersonName`,
  `Fraction`, `Length`, `Time`, `Url`, …). A `DataType` is a **scalar/leaf** value: one canonical
  `toString()`, string-based `equals()`. Multi-field composites (`Person`, `Organization` in
  `src/Data`, built from several named `Data\Types` values) are `DataObject`s instead — no single
  canonical string form, no scalar equality. Both get the same `from()`/`tryFrom()`/`fromArray()`
  construction convention.
- `src/Data/Contracts/*` — the interfaces a type opts into: `Arrayable` (→ array), `Htmlable`
  (→ `toHtml()`), `Localizable` (→ locale-aware output), `Schemable` (→ schema.org/JSON-LD),
  `Jsonable`, `Comparable`, `Enumerable`.
- `src/Data/Collections/*` — typed collections over those value objects (`WeekDays`, `TimeRanges`).
- `src/Data/Concerns/*` — shared trait behavior mixed into the types (`ComparesValues`,
  `EnumeratesValues`, `TransformsStrings`, …).
- Construction is always via static factories: `from()` (throws on invalid input), `tryFrom()`
  (returns `null`), `fromArray()`.
- `toHtml()` renders through a Kirby snippet (`App::instance()->snippet('schema/{kebab-name}', [
  'item' => $this, 'attrs' => $attributes])`), not inline `Html::tag()` calls — this is what makes
  the markup project-overridable (a project's `site/snippets/schema/...` file wins over the plugin's).
  Nested composition happens inside the snippet (calling `$item->child->toHtml()`), not in PHP.
- `src/Cms/*` — traits/contracts used by Kirby page/file/block models: `Cms/Contracts/*` interfaces
  (`HasKeyVisual`, `HasExcerpt`), `Cms/Concerns/*` traits (`HasLink`, `HasMediaFile`, `HasBlocks`,
  `HasKeyVisual`, `HasCoverImage`, …), and the block classes under `src/Cms/Blocks`. Trait/interface
  names mirror their primary method (`HasLink` → `link()`, `HasKeyVisual` → `keyVisual()`) rather
  than chasing a fixed word count; split a trait instead of keeping a vague bundle name once it no
  longer has one clear primary method. `Concerns\HasKeyVisual` takes an abstract `blocks()` instead
  of implementing its own, so a page can implement it to generate a `<link rel="preload">` for an
  eager-loaded hero image regardless of how that page sources its `blocks()`.

This replaces the v1 `*Factory`/`*Formatter` classes (`AddressFactory`, `OpeningHoursFactory`,
`CurrencyFormatter`, …), which are gone on this branch.

## Media handling is out of scope here

Responsive image/video rendering (srcset, sizes, presets) lives entirely in the separate
[`hksagentur/kirby-media-kit`](https://github.com/hksagentur/kirby-media-kit) package — kirby-schema
does not depend on it and does not re-implement it. `src/Data/Types/ImageUrl.php` and `FileUrl.php`
are unrelated, thin wrappers that turn a plain file URL/UID into an `<img>`/`<a>` tag; don't grow
them into a responsive-image feature.

## Conventions

- PHP `>=8.3`. Formatting/linting via [Laravel Pint](https://github.com/laravel/pint): run
  `composer format` to fix, `composer lint` to check (also the only CI job, in
  `.github/workflows/format.yml`).
- Blueprint YAML files use tabs for indentation and Kirby's `*: value` (per-locale) and
  `@namespace/...` (blueprint reference) syntax — these look invalid to a generic YAML linter but
  are handled by Kirby's own blueprint loader; don't "fix" them.
- PHPUnit is the test runner (`composer test`, config in `phpunit.xml`, tests under `tests/`,
  bootstrapped via `tests/bootstrap.php` which boots a `Kirby\Cms\App`). Coverage is still partial —
  see [TODO.md](TODO.md) Track A/B for what's untested — so also verify changes by booting Kirby
  against the plugin or via Pint/`php -l` where tests don't yet reach.
- Translations live in `lang/de.php` and `lang/en.php`, keyed as
  `hksagentur.schema.<blueprint-kind>.<name>.<field>`; add keys in both files together, and keep
  them roughly alphabetically ordered per section as the existing files do.

## Where things live

| Concern | Path |
|---|---|
| Value objects, contracts, collections | `src/Data/` |
| Kirby model traits/contracts | `src/Cms/` |
| Blocks | `src/Cms/Blocks/`, blueprints in `config/blueprints/blocks/` |
| Field/collection/structure-object methods | `config/methods/` |
| Blueprint fields/sections/tabs/pages/files | `config/blueprints/` |
| Plugin registration, options | `index.php` |
| Translations | `lang/` |

## Before picking up paused work

Check [TODO.md](TODO.md) for concrete unfinished methods/classes (several `Data\Types\OpeningHours*`
methods and the `toDataType`/`toDataObject` field methods currently throw or are empty bodies) and
[ROADMAP.md](ROADMAP.md) for the milestones planned before an alpha release.

A full architecture review of `src/Data/Types`, `src/Data/Contracts`, `src/Cms`, and
`config/methods` was done on 2026-09-01, including a fresh-perspective pass evaluating the plugin as
a consumer would. Its conclusions (what to keep, what was missing, several concrete bugs found) are
folded into this file, [ROADMAP.md](ROADMAP.md) ("Design decisions from the 2026-09-01 architecture
review"), and [TODO.md](TODO.md) — no separate handover document needed for it anymore.
