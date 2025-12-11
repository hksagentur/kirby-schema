<?php

namespace Hks\Schema\Formatter;

use Hks\Schema\Toolkit\I18n;
use Kirby\Cms\Html;
use Kirby\Toolkit\A;
use Kirby\Toolkit\Date;
use Kirby\Toolkit\Str;
use Spatie\OpeningHours\OpeningHours;
use Spatie\OpeningHours\OpeningHoursForDay;
use Spatie\OpeningHours\TimeRange;

/**
 * @extends Formatter<OpeningHours>
 */
class OpeningHoursMicrodataFormatter extends Formatter
{
    public function defaults(): array
    {
        return [
            'as' => 'dl',
            'format' => 'full',
            'attributes' => [
                'aria-label' => I18n::translate('hksagentur.schema.formatter.openingHours.label'),
            ],
        ];
    }

    public function format(mixed $openingHours): string
    {
        return Html::tag(
            name: $this->option('as'),
            content: $this->formatOpeningHours($openingHours),
            attr: $this->option('attributes'),
        );
    }

    protected function formatOpeningHours(OpeningHours $openingHours): array
    {
        $content = [];

        foreach ($openingHours->forWeekConsecutiveDays() as ['days' => $days, 'opening_hours' => $openingHoursForDay]) {
            $content[] = $this->formatOpeningHoursForDays($days, $openingHoursForDay);
        }

        foreach ($openingHours->exceptions() as $date => $openingHoursForDay) {
            $dateTime = Date::createFromFormat('Y-m-d', $date);

            if ($dateTime < Date::today()) {
                continue;
            }

            $content[] = $this->formatOpeningHoursException($date, $openingHoursForDay);
        }

        return $content;
    }

    protected function formatOpeningHoursForDays(array $days, OpeningHoursForDay $openingHoursForDay): string
    {
        return Html::tag(in_array($this->option('as'), ['ul', 'ol']) ? 'li' : 'div', [
            $this->formatOpeningHoursDateRange($days),
            $this->formatOpeningHoursTimeRange($openingHoursForDay),
        ], [
            'itemprop' => 'openingHoursSpecification',
            'itemscope' => '',
            'itemtype' => 'https://schema.org/OpeningHoursSpecification',
        ]);
    }

    protected function formatOpeningHoursException(string $date, OpeningHoursForDay $openingHoursForDay): string
    {
        return Html::tag(in_array($this->option('as'), ['ul', 'ol']) ? 'li' : 'div', [
            $this->formatOpeningHoursTimeRangeValidity($date, $openingHoursForDay),
            $this->formatOpeningHoursTimeRange($openingHoursForDay),
            ...A::wrap($openingHoursForDay->getData()),
        ], [
            'itemprop' => 'openingHoursSpecification',
            'itemscope' => '',
            'itemtype' => 'https://schema.org/OpeningHoursSpecification',
        ]);
    }

    protected function formatOpeningHoursDateRange(array $days): string
    {
        $startDay = Str::ucfirst(A::first($days));
        $endDay = Str::ucfirst(A::last($days));

        $dayNames = I18n::getDayNames('en_US', format: 'full');
        $shortDayNames = I18n::getDayNames('en_US', format: 'tiny');

        $translatedDayNames = array_combine($dayNames, I18n::getDayNames(
            locale: $this->option('locale'),
            format: $this->option('format'),
        ));

        $formattedDateRange = $startDay === $endDay ? [
            Html::tag('time', $translatedDayNames[$startDay])
        ] : [
            Html::tag('time', $translatedDayNames[$startDay]),
            '–',
            Html::tag('time', $translatedDayNames[$endDay]),
        ];

        $formattedMetaTags = A::map($days, fn (string $day) => Html::tag('meta', attr: [
            'itemprop' => 'dayOfWeek',
            'content' => $shortDayNames[$day],
        ]));

        return Html::tag(
            name: in_array($this->option('as'), ['dl']) ? 'dt' : 'div',
            content: [
                ...$formattedDateRange,
                ...$formattedMetaTags,
            ],
        );
    }

    protected function formatOpeningHoursTimeRange(OpeningHoursForDay $openingHoursForDay): string
    {
        return Html::tag(in_array($this->option('as'), ['dl']) ? 'dd' : 'div', $openingHoursForDay->isEmpty() ? [
            I18n::translate('hksagentur.schema.formatter.openingHours.closed'),
        ] : [
            ...$openingHoursForDay->map(fn (TimeRange $timeRange) => [
                Html::tag('time', $timeRange->start()->format(), [
                    'itemprop' => 'opens',
                    'datetime' => $timeRange->start()->format(),
                ]),
                '–',
                Html::tag('time', $timeRange->end()->format(), [
                    'itemprop' => 'closes',
                    'datetime' => $timeRange->end()->format(),
                ]),
            ]),
        ]);
    }

    protected function formatOpeningHoursTimeRangeValidity(string $date, OpeningHoursForDay $openingHoursForDay): string
    {
        return Html::tag(in_array($this->option('as'), ['dl']) ? 'dt' : 'div', [
            Html::tag('time', [
                Date::createFromFormat('Y-m-d', $date)->format('d.m.Y'),
            ], [
                'itemprop' => 'validFrom validThrough',
                'datetime' => $date,
            ]),
        ]);
    }
}
