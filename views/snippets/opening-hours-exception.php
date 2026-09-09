<?php /** @var \Hks\Schema\Data\Types\OpeningHoursException $item */ ?>

<span<?= attr($attrs ?? [], ' ') ?>>
    <?php if ($item->hasTimeRange()) : ?>
        <?= $item->timeRange()->toHtml() ?>
    <?php else : ?>
        <?= t('hksagentur.schema.openingHours.closed') ?>
    <?php endif ?>
</span>
