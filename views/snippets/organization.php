<?php /** @var \Hks\Schema\Data\Organization $item */ ?>

<div <?= attr([
    'itemscope' => true,
    'itemtype' => 'https://schema.org/LocalBusiness',
    ...$attrs ?? [],
]) ?>>
    <div <?= attr([
        'itemprop' => 'name',
        'hidden' => true,
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

    <?= $item->email()?->toHtml([
        'itemprop' => 'email',
    ]) ?>

    <?= $item->telephone()?->toHtml([
        'itemprop' => 'telephone',
    ]) ?>

    <?= $item->fax()?->toHtml([
        'itemprop' => 'faxNumber',
    ]) ?>
</div>
