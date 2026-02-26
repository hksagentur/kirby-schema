<?php /** @var \Hks\Schema\Data\Person $item */ ?>

<div <?= attr([
    'itemscope' => true,
    'itemtype' => 'https://schema.org/Person',
    ...$attrs ?? [],
]) ?>>
    <div <?= attr([
        'itemprop' => 'name',
    ]) ?>>
        <?= esc($item->name()->displayName()) ?>
    </div>

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
