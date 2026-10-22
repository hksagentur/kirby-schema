<?php /** @var \Hks\Schema\Data\Types\Area $item */ ?>

<data <?= attr([
    'value' => $item->toSquareMeters()->value(),
    'data-unit' => $item->unit()->symbol(),
    ...$attrs ?? [],
]) ?>>
    <?= esc($item->toLocaleString()) ?>
</data>
