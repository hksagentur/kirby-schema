<?php /** @var \Hks\Schema\Data\Place $item */ ?>

<div <?= attr([
    'class' => [
        'h-card',
        ...A::wrap($attrs['class'] ?? []),
    ],
    'itemscope' => true,
    'itemtype' => 'https://schema.org/Place',
    ...A::without($attrs ?? [], 'class'),
]) ?>>
    <div <?= attr([
        'class' => 'p-name',
        'itemprop' => 'name',
    ]) ?>>
        <?= esc($item->name()) ?>
    </div>

    <?= $item->address()?->toHtml([
        'class' => 'p-adr',
        'itemprop' => 'address',
    ]) ?>

    <?= $item->coordinates()?->toHtml([
        'class' => 'p-geo',
        'itemprop' => 'geo',
        'hidden' => true,
    ]) ?>
</div>
