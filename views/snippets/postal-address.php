<?php /** @var \Hks\Schema\Data\Types\PostalAddress $item */ ?>

<div <?= attr([
    'itemscope' => true,
    'itemtype' => 'https://schema.org/PostalAddress',
    'translate' => 'no',
    ...$attrs ?? [],
]) ?>>
    <?= $formatted ?>
</div>
