<?php /** @var \Hks\Schema\Data\Types\OpeningHoursSpecification $item */ ?>

<time<?= attr([
    'datetime' => $item->toString(),
    ...$attrs ?? [],
], ' ') ?>>
    <?= $item->toLocaleString() ?>
</time>
