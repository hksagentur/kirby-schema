<?php

namespace Hks\Schema\Formatter;

use Hks\Schema\Toolkit\I18n;
use Kirby\Toolkit\A;
use Kirby\Toolkit\Date;
use Kirby\Toolkit\Str;
use Spatie\OpeningHours\OpeningHours;
use Spatie\OpeningHours\OpeningHoursForDay;

/**
 * @extends Formatter<OpeningHours>
 */
class OpeningHoursTextFormatter extends Formatter
{
    public function defaults(): array
    {
        return [
            'format' => 'full',
        ];
    }

    public function format(mixed $openingHours): string
    {
        $lines = [];

        foreach ($openingHours->forWeekConsecutiveDays() as ['days' => $days, 'opening_hours' => $openingHoursForDay]) {
            $lines[] = $this->formatOpeningHoursForDays($days, $openingHoursForDay);
        }

        foreach ($openingHours->exceptions() as $date => $openingHoursForDay) {
            $dateTime = Date::createFromFormat('Y-m-d', $date);

            if ($dateTime < Date::today()) {
                continue;
            }

            $lines[] = $this->formatOpeningHoursException($date, $openingHoursForDay);
        }

        return implode("\n", $lines);
    }

    protected function formatOpeningHoursForDays(array $days, OpeningHoursForDay $openingHoursForDay): string
    {
        return sprintf(
            '%s: %s',
            $this->formatOpeningHoursDateRange($days),
            $this->formatOpeningHoursTimeRange($openingHoursForDay),
        );
    }

    protected function formatOpeningHoursException(string $date, OpeningHoursForDay $openingHoursForDay): string
    {
        return sprintf(
            '%s: %s %s',
            Date::createFromFormat('Y-m-d', $date)->format('d.m.Y'),
            $this->formatOpeningHoursTimeRange($openingHoursForDay),
            ...A::wrap($openingHoursForDay->getData()),
        );
    }

    protected function formatOpeningHoursDateRange(array $days): string
    {
        $translatedDayNames = array_combine(
            I18n::getDayNames(locale: 'en_US', format: 'full'),
            I18n::getDayNames(locale: $this->option('locale'), format: $this->option('format'))
        );

        $startDay = Str::ucfirst(A::first($days));
        $endDay = Str::ucfirst(A::last($days));

        $translatedStartDay = $translatedDayNames[$startDay];
        $translatedEndDay = $translatedDayNames[$endDay];

        if ($startDay === $endDay) {
            return $translatedStartDay;
        }

        return sprintf('%s-%s', $translatedStartDay, $translatedEndDay);
    }

    protected function formatOpeningHoursTimeRange(OpeningHoursForDay $openingHoursForDay): string
    {
        if ($openingHoursForDay->isEmpty()) {
            return I18n::translate('hksagentur.schema.formatter.openingHours.closed');
        }

        return (string) $openingHoursForDay;
    }
}
