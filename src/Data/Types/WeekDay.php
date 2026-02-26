<?php

namespace Hks\Schema\Data\Types;

use DateTime;
use DateTimeInterface;
use Hks\Schema\Data\Contracts\Localizable;
use IntlCalendar;
use Kirby\Toolkit\A;
use Kirby\Toolkit\Date;
use Kirby\Toolkit\Str;
use Stringable;

enum WeekDay: int implements Localizable, Stringable
{
    case Monday = 1;
    case Tuesday = 2;
    case Wednesday = 3;
    case Thursday = 4;
    case Friday = 5;
    case Saturday = 6;
    case Sunday = 7;

    public static function all(): array
    {
        return array_column(static::cases(), column_key: null, index_key: 'name');
    }

    public static function of(DateTimeInterface $date): static
    {
        return static::from((int) $date->format('N'));
    }

    public static function fromName(string $name): ?static
    {
        return A::find(static::cases(), fn (self $weekDay) => strcasecmp($weekDay->name(), $name) === 0);
    }

    public static function fromDate(DateTimeInterface $date): ?static
    {
        return static::fromDayOfWeek((int) $date->format('w'));
    }

    public static function fromDayOfWeek(int $dayOfWeek): ?static
    {
        return static::tryFrom($dayOfWeek === 0 ? 7 : $dayOfWeek);
    }

    public static function fromDayOfWeekIso(int $dayOfWeek): ?static
    {
        return static::tryFrom($dayOfWeek);
    }

    public static function startOfWeek(?string $locale = null): self
    {
        return static::fromDayOfWeek(Date::firstWeekday($locale));
    }

    public static function endOfWeek(?string $locale = null): self
    {
        return static::fromDayOfWeek((static::startOfWeek($locale)->dayOfWeek() + 6) % 7);
    }

    public function isStartOfWeek(?string $locale = null): bool
    {
        return static::startOfWeek($locale) === $this;
    }

    public function isEndOfWeek(?string $locale = null): bool
    {
        return static::endOfWeek($locale) === $this;
    }

    public function isWeekend(?string $locale = null): bool
    {
        return ! $this->isWeekday($locale);
    }

    public function isWeekday(?string $locale = null): bool
    {
        if (! class_exists('IntlCalendar')) {
            return ! in_array($this, [self::Saturday, self::Sunday]);
        }

        $calendar = IntlCalendar::createInstance(locale: $locale ?? I18n::locale());
        $type = $calendar->getDayOfWeekType($this->dayOfWeekIcu());

        return $type === IntlCalendar::DOW_TYPE_WEEKDAY;
    }

    public function is(self $other): bool
    {
        return $this === $other;
    }

    public function compare(self $other): int
    {
        return $this->dayOfWeekIso() <=> $other->dayOfWeekIso();
    }

    public function label(?string $locale = null): string
    {
        return $this->toLocaleString($locale);
    }

    public function key(): string
    {
        return Str::lower($this->name());
    }

    public function name(): string
    {
        return $this->toLocaleString(locale: 'en', options: ['style' => 'full']);
    }

    public function shortName(): string
    {
        return $this->toLocaleString(locale: 'en', options: ['style' => 'short']);
    }

    public function dayOfWeek(): int
    {
        return $this->value % 7;
    }

    public function dayOfWeekIso(): int
    {
        return $this->value;
    }

    public function dayOfWeekIcu(): int
    {
        return $this->dayOfWeek() + 1;
    }

    public function current(?DateTimeInterface $now = null): DateTimeInterface
    {
        return $now?->modify("{$this->name()} this week") ?? new DateTime("{$this->name()} this week");
    }

    public function next(?DateTimeInterface $now = null): DateTimeInterface
    {
        return $now?->modify("next {$this->name()}") ?? new DateTime("next {$this->name()}");
    }

    public function precedes(self $other): bool
    {
        return $other->follows($this);
    }

    public function follows(self $other): bool
    {
        return $this->dayOfWeekIso() === ($other->dayOfWeekIso() % 7) + 1;
    }

    public function toString(): string
    {
        return $this->name();
    }

    public function toLocaleString(?string $locale = null, array $options = []): string
    {
        $style = match ($options['style'] ?? null) {
            'full', 'short', => $options['style'],
            default => 'short',
        };

        return I18n::translate(
            key: "hksagentur.schema.weekDay.{$this->key()}.{$style}",
            fallback: $this->name(),
            locale: $locale,
        );
    }

    public function __toString(): string
    {
        return $this->toString();
    }
}
