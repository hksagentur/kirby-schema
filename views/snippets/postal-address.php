<?php /** @var \Hks\Schema\Data\Types\PostalAddress $item */ ?>

<div <?= attr([
    'itemscope' => true,
    'itemtype' => 'https://schema.org/PostalAddress',
    'translate' => 'no',
    ...$attrs ?? [],
]) ?>>
    <?= $item->addressLines(
        fn (string $value, string $field): string => Html::tag('span', $value, ['itemprop' => $field]),
    )->join("<br>\n") ?>
</div>
