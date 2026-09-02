<?php /** @var \Hks\Schema\Data\Types\OpeningHours $item */ ?>

<dl <?= attr([
    'itemscope' => true,
    'itemtype' => 'https://schema.org/OpeningHoursSpecification',
    ...$attrs ?? [],
]) ?>>
    <?php foreach ($item->specifications() as $specification) : ?>
        <dt>
            <?php foreach ($specification->weekDays()->all() as $weekDay) : ?>
                <time itemprop="dayOfWeek" datetime="<?= $weekDay->current()->format('Y-m-d') ?>">
                    <?= esc($weekDay->toLocaleString()) ?>
                </time>
            <?php endforeach ?>
        </dt>
        <dd>
            <?= $specification->toHtml() ?>
        </dd>
    <?php endforeach ?>

    <?php foreach ($item->exceptions() as $exception) : ?>
        <dt><?= esc($exception->date()->format('Y-m-d')) ?></dt>
        <dd><?= $exception->toHtml() ?></dd>
    <?php endforeach ?>
</dl>
