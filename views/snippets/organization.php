<?php /** @var \Hks\Schema\Data\Organization $item */ ?>

<div <?= attr([
    'class' => [
        'h-card',
        ...A::wrap($attrs['class'] ?? []),
    ],
    'itemscope' => true,
    'itemtype' => 'https://schema.org/Organization',
    ...A::without($attrs ?? [], 'class'),
]) ?>>
    <div <?= attr([
        'class' => [
            'p-name',
            'p-org',
        ],
        'itemprop' => 'name',
        'hidden' => true,
    ]) ?>>
        <?= esc($item->name()) ?>
    </div>

    <?php if ($alternateName = $item->alternateName()) : ?>
        <meta <?= attr([
            'itemprop' => 'alternateName',
            'content' => $alternateName,
        ]) ?>>
    <?php endif ?>

    <?= $item->address()?->toHtml([
        'class' => 'p-adr',
        'itemprop' => 'address',
    ]) ?>

    <?= $item->coordinates()?->toHtml([
        'class' => 'p-geo',
        'itemprop' => 'geo',
        'hidden' => true,
    ]) ?>

    <?= $item->email()?->toHtml([
        'class' => 'u-email',
        'itemprop' => 'email',
    ]) ?>

    <?= $item->telephone()?->toHtml([
        'class' => 'p-tel',
        'itemprop' => 'telephone',
    ]) ?>

    <?= $item->fax()?->toHtml([
        'itemprop' => 'faxNumber',
    ]) ?>

    <?php foreach ($item->contactPoints() as $contactPoint) : ?>
        <?= $contactPoint->toHtml([
            'itemprop' => 'contactPoint',
        ]) ?>
    <?php endforeach ?>
</div>
