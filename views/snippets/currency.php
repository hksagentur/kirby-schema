<?php /** @var \Hks\Schema\Data\Types\Currency $item */ ?>

<abbr <?= attr([
    'title' => $item->toLocaleString(),
    ...$attrs ?? [],
]) ?>><?= esc($item->code()) ?></abbr>
