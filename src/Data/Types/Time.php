<?php

namespace Hks\Schema\Data\Types;

use DateTime;
use DateTimeInterface;
use DateTimeZone;
use Hks\Schema\Data\Concerns\ComparesMoments;
use Hks\Schema\Data\Contracts\Arrayable;
use Hks\Schema\Data\Contracts\Htmlable;
use Hks\Schema\Data\Contracts\Localizable;
use Hks\Schema\Data\Exceptions\TypeMismatchException;
use Hks\Schema\Data\Exceptions\ValidationException;
use IntlDateFormatter;
use Kirby\Toolkit\Html;
use Kirby\Toolkit\I18n;
use Kirby\Toolkit\Str;
use Throwable;

readonly class Time extends DataType implements Arrayable, Htmlable, Localizable
{
    use ComparesMoments;

    public const string SEPARATOR = ':';

    public const int SECONDS_PER_MINUTE = 60;
    public const int SECONDS_PER_HOUR = 3_600;
    public const int SECONDS_PER_DAY = 86_400;
    public const int MINUTES_PER_HOUR = 60;
    public const int MINUTES_PER_DAY = 1_440;
    public const int HOURS_PER_DAY = 24;

    public function __construct(
        public int $hour = 0,
        public int $minute = 0,
        public int $second = 0,
    ) {
        if (
            ($hour < 0 || $hour >= self::HOURS_PER_DAY) ||
            ($minute < 0 || $minute >= self::MINUTES_PER_HOUR) ||
            ($second < 0 || $second >= self::SECONDS_PER_MINUTE)
        ) {
            throw new ValidationException(sprintf(
                'The given value "%s" does not represent a valid time of a day.',
                $this->toIsoString(),
            ));
        }
    }

    public static function from(int|string|array|self|DateTimeInterface $value): self
    {
        return match (true) {
            $value instanceof static => $value,
            $value instanceof DateTimeInterface => static::fromDate($value),
            is_int($value) => static::fromSecondOfDay($value),
            is_array($value) => static::fromArray($value),
            default => static::fromIsoString((string) $value),
        };
    }

    public static function tryFrom(int|string|array|self|DateTimeInterface $value): ?static
    {
        try {
            return static::from($value);
        } catch (Throwable) {
            return null;
        }
    }

    public static function fromArray(array $value): static
    {
        [$hour, $minute, $second] = match (true) {
            isset($value['hour']) => [
                $value['hour'],
                $value['minute'] ?? 0,
                $value['second'] ?? 0,
            ],
            isset($value[0]) => [
                $value[0],
                $value[1] ?? 0,
                $value[2] ?? 0,
            ],
            default => throw new ValidationException(
                'The given array does not represent a valid time of a day.'
            ),
        };

        return new static((int) $hour, (int) $minute, (int) $second);
    }

    public static function fromDate(DateTimeInterface $date): static
    {
        $hour = (int) $date->format('H');
        $minute = (int) $date->format('i');
        $second = (int) $date->format('s');

        return new static($hour, $minute, $second);
    }

    public static function fromIsoString(string $value): static
    {
        $parts = Str::split($value, self::SEPARATOR);

        if (count($parts) < 2 || count($parts) > 3) {
            throw new ValidationException(sprintf(
                'The given value "%s" does not represent a valid time of a day, expected format "HH:MM" or "HH:MM:SS".',
                $value,
            ));
        }

        $hour = (int) ($parts[0] ?? 0);
        $minute = (int) ($parts[1] ?? 0);
        $second = (int) ($parts[2] ?? 0);

        return new static($hour, $minute, $second);
    }

    public static function fromSecondOfDay(int $secondOfDay): static
    {
        $hour = intdiv($secondOfDay, self::SECONDS_PER_HOUR) % self::HOURS_PER_DAY;
        $minute = intdiv($secondOfDay % self::SECONDS_PER_HOUR, self::SECONDS_PER_MINUTE);
        $second = $secondOfDay % self::SECONDS_PER_MINUTE;

        return new static($hour, $minute, $second);
    }

    public static function now(?DateTimeZone $timezone = null): static
    {
        return static::fromDate(new DateTime(timezone: $timezone));
    }

    public static function noon(): static
    {
        return static::fromSecondOfDay(12 * self::SECONDS_PER_HOUR);
    }

    public static function midnight(): static
    {
        return static::fromSecondOfDay(0);
    }

    public function isNoon(): bool
    {
        return $this->valueOf() === 12 * self::SECONDS_PER_HOUR;
    }

    public function isMidnight(): bool
    {
        return $this->valueOf() === 0;
    }

    public function compare(mixed $other): int
    {
        if (! ($other instanceof static)) {
            throw TypeMismatchException::between($this, $other);
        }

        return $this->toSecondOfDay() <=> $other->toSecondOfDay();
    }

    public function hour(): int
    {
        return $this->hour;
    }

    public function minute(): int
    {
        return $this->minute;
    }

    public function second(): int
    {
        return $this->second;
    }

    public function valueOf(): int
    {
        return $this->toSecondOfDay();
    }

    public function format(int|string|null $format = null, ?string $locale = null): string
    {
        $format ??= IntlDateFormatter::SHORT;
        $locale ??= I18n::locale();

        $formatter = match (true) {
            is_int($format) => IntlDateFormatter::create(
                locale: $locale,
                dateType: IntlDateFormatter::NONE,
                timeType: $format,
            ),
            default => new IntlDateFormatter(
                locale: $locale,
                dateType: IntlDateFormatter::NONE,
                timeType: IntlDateFormatter::NONE,
                pattern: $format,
            ),
        };

        return $formatter->format($this->toDateTime());
    }

    public function toSecondOfDay(): int
    {
        return (
            $this->hour * self::SECONDS_PER_HOUR +
            $this->minute * self::SECONDS_PER_MINUTE +
            $this->second
        );
    }

    public function toDateTime(?DateTimeInterface $date = null): DateTimeInterface
    {
        return DateTime::createFromInterface(
            $date ?? new DateTime('@0')
        )->setTime($this->hour, $this->minute, $this->second);
    }

    public function toArray(): array
    {
        return [
            'hour' => $this->hour,
            'minute' => $this->minute,
            'second' => $this->second,
        ];
    }

    public function toString(): string
    {
        return $this->toIsoString();
    }

    public function toIsoString(): string
    {
        return sprintf(
            '%02d%s%02d%s%02d',
            $this->hour,
            self::SEPARATOR,
            $this->minute,
            self::SEPARATOR,
            $this->second,
        );
    }

    public function toLocaleString(?string $locale = null, array $options = []): string
    {
        return $this->format(locale: $locale);
    }

    public function toHtml(array $attributes = []): string
    {
        return Html::tag('time', $this->toString(), $attributes);
    }

    public function jsonSerialize(): string
    {
        return $this->toIsoString();
    }
}
