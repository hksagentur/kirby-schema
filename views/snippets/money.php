<?php /** @var \Hks\Schema\Data\Types\Money $item */ ?>

<div <?= attr([
    'itemscope' => true,
    'itemtype' => 'https://schema.org/MonetaryAmount',
    ...$attrs ?? [],
]) ?>>
    <meta <?= attr([
        'itemprop' => 'currency',
        'content' => $item->currency()->name(),
    ]) ?>>
    <meta <?= attr([
        'itemprop' => 'value',
        'content' => $item->toMajorUnits(),
    ]) ?>>

    <?= esc($item->toLocaleString()) ?>
</div>
