<?php /** @var \Hks\Schema\Data\Types\Time $item */ ?>

<time<?= attr([
    'datetime' => $item->toString(),
    ...$attrs ?? [],
], ' ') ?>>
    <?= esc($item->toString()) ?>
</time>
