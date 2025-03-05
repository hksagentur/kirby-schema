<?php

namespace Hks\Schema\Data;

use Kirby\Cms\App;
use Kirby\Toolkit\A;
use Kirby\Toolkit\Html;
use Kirby\Toolkit\I18n;
use Spatie\OpeningHours\OpeningHours;

class OpeningHoursFormatter
{
    public function format(OpeningHours $openingHours, array $options = []): string
    {
        $options = A::merge([
            'locale' => App::instance()->language()->locale(LC_ALL),
            'abbreviate' => false,
            'attributes' => [
                'class' => ['meta'],
            ],
        ], $options);

        $elements = [];

        foreach ($openingHours->forWeekConsecutiveDays() as [
            'days' => $days,
            'opening_hours' => $timeRanges,
        ]) {
            $start = A::first($days);
            $end = A::last($days);

            if (empty($options['abbreviate'])) {
                $localizedStart = I18n::translate(
                    key: 'hksagentur.schema.day.'.$start.'.long',
                    locale: $options['locale'],
                );

                $localizedEnd = I18n::translate(
                    key: 'hksagentur.schema.day.'.$end.'.long',
                    locale: $options['locale'],
                );
            } else {
                $localizedStart = I18n::translate(
                    key: 'hksagentur.schema.day.'.$start.'.short',
                    locale: $options['locale'],
                );

                $localizedEnd = I18n::translate(
                    key: 'hksagentur.schema.day.'.$end.'.short',
                    locale: $options['locale'],
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
                    content: I18n::translate('hksagentur.schema.status.hours.closed'),
                    attr: ['class' => 'meta__value'],
                );
            }
        }

        return Html::tag('dl', $elements, $options['attributes']);
    }
}
