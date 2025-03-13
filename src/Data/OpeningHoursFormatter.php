<?php

namespace Hks\Schema\Data;

use Kirby\Toolkit\A;
use Kirby\Toolkit\Html;
use Kirby\Toolkit\I18n;
use Spatie\OpeningHours\OpeningHours;

class OpeningHoursFormatter extends Formatter
{
    public function format(OpeningHours $openingHours, array $options = []): string
    {
        $locale = $options['locale'] ?? static::defaultLocale();
        $abbreviate = $options['abbreviate'] ?? false;

        $elements = [];

        foreach ($openingHours->forWeekConsecutiveDays() as [
            'days' => $days,
            'opening_hours' => $timeRanges,
        ]) {
            $start = A::first($days);
            $end = A::last($days);

            if ($abbreviate) {
                $localizedStart = I18n::translate(
                    key: 'hksagentur.schema.day.'.$start.'.long',
                    locale: $locale,
                );

                $localizedEnd = I18n::translate(
                    key: 'hksagentur.schema.day.'.$end.'.long',
                    locale: $locale,
                );
            } else {
                $localizedStart = I18n::translate(
                    key: 'hksagentur.schema.day.'.$start.'.short',
                    locale: $locale,
                );

                $localizedEnd = I18n::translate(
                    key: 'hksagentur.schema.day.'.$end.'.short',
                    locale: $locale,
                );
            }

            if ($start === $end) {
                $elements[] = Html::tag(
                    name: 'dt',
                    content: $localizedStart,
                    attr: ['class' => 'meta__key'],
                );
            } else {
                $elements[] = Html::tag(
                    name: 'dt',
                    content: sprintf('%s–%s', $localizedStart, $localizedEnd),
                    attr: ['class' => 'meta__key'],
                );
            }

            if (count($timeRanges) > 0) {
                foreach ($timeRanges as $timeRange) {
                    $elements[] = Html::tag(
                        name: 'dd',
                        content: sprintf('%s–%s', $timeRange->start(), $timeRange->end()),
                        attr: ['class' => 'meta__value'],
                    );
                }
            } else {
                $elements[] = Html::tag(
                    name: 'dd',
                    content: I18n::translate(
                        key: 'hksagentur.schema.status.hours.closed',
                        locale: $locale,
                    ),
                    attr: ['class' => 'meta__value'],
                );
            }
        }

        return Html::tag('dl', $elements, $options['attributes'] ?? []);
    }
}
