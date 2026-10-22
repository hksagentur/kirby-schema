<?php /** @var \Hks\Schema\Data\Types\OpeningHours $item */ ?>

<dl <?= attr([...$attrs ?? []]) ?>>
    <?php foreach ($item->specifications() as $specification) : ?>
        <div <?= attr([
            'itemscope' => true,
            'itemtype' => 'https://schema.org/OpeningHoursSpecification',
        ]) ?>>
            <dt>
                <?php foreach ($specification->weekDays() as $weekDay) : ?>
                    <meta <?= attr([
                        'itemprop' => 'dayOfWeek',
                        'content' => $weekDay->toIriString(),
                    ]) ?>>
                <?php endforeach ?>

                <?= esc($specification->weekDays()->toLocaleString()) ?>
            </dt>
            <dd>
                <?= $specification->toHtml() ?>
            </dd>
        </div>
    <?php endforeach ?>

    <?php foreach ($item->exceptions() as $exception) : ?>
        <div>
            <dt>
                <?= esc($exception->date()->format(\Hks\Schema\Data\Types\OpeningHoursException::ISO_DATE_FORMAT)) ?>
            </dt>
            <dd>
                <?= $exception->toHtml() ?>
            </dd>
        </div>
    <?php endforeach ?>
</dl>
