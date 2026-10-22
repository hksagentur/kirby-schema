<?php /** @var \Hks\Schema\Data\ContactPoint $item */ ?>

<div <?= attr([
    'itemscope' => true,
    'itemtype' => 'https://schema.org/ContactPoint',
    ...$attrs ?? [],
]) ?>>
    <?php if ($type = $item->type()) : ?>
        <div <?= attr([
            'itemprop' => 'contactType',
        ]) ?>>
            <?= esc($type) ?>
        </div>
    <?php endif ?>

    <?= $item->telephone()?->toHtml([
        'itemprop' => 'telephone',
    ]) ?>

    <?= $item->email()?->toHtml([
        'itemprop' => 'email',
    ]) ?>
</div>
