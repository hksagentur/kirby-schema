<?php /** @var \Hks\Schema\Data\Place $item */ ?>

<div <?= attr([
    'itemscope' => true,
    'itemtype' => 'https://schema.org/Place',
    ...$attrs ?? [],
]) ?>>
    <div <?= attr([
        'itemprop' => 'name',
    ]) ?>>
        <?= esc($item->name()) ?>
    </div>

    <?= $item->address()?->toHtml([
        'itemprop' => 'address',
    ]) ?>

    <?= $item->coordinates()?->toHtml([
        'itemprop' => 'geo',
        'hidden' => true,
    ]) ?>
</div>
