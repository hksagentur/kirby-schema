<?php /** @var \Hks\Schema\Data\Types\Length $item */ ?>

<data <?= attr([
    'value' => $item->toMeters()->value(),
    'data-unit' => $item->unit()->symbol(),
    ...$attrs ?? [],
]) ?>>
    <?= esc($item->toLocaleString()) ?>
</data>
