<?php /** @var \Hks\Schema\Data\Types\OpeningHours $item */ ?>

<dl <?= attr([
    'itemscope' => true,
    'itemtype' => 'https://schema.org/OpeningHoursSpecification',
    ...$attrs ?? [],
]) ?>>
    <?php foreach ($item->forThisWeek() as [$weekDay, $openingHours]) : ?>
        <dt>
            <time itemprop="dayOfWeek" datetime="<?= $weekDay->current()->format('Y-m-d') ?>">
                <?= $weekDay->toLocaleString() ?>
            </time>
        </dt>
        <dd>
            <?php if (count($openingHours) > 0) : ?>
                <?php foreach ($openingHours as [$startDate, $endDate]) : ?>
                    <time itemprop="opens" datetime="<?= $startDate->format(DateTime::W3C) ?>">
                        <?= $startDate->format('H:i') ?>
                    </time>
                    -
                    <time itemprop="closes" datetime="<?= $endDate->format(DateTime::W3C) ?>">
                        <?= $endDate->format('H:i') ?>
                    </time><br>
                <?php endforeach ?>
            <?php else : ?>
                <?= t('hksagentur.schema.openingHours.closed') ?>
            <?php endif ?>
        </dd>
    <?php endforeach ?>
</dl>
