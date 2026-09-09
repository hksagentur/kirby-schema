<?php /** @var \Hks\Schema\Data\Types\TimeRange $item */ ?>

<data <?= attr([
    'value' => $item->toString(),
    ...$attrs ?? [],
]) ?>>
    <?= $item->start()->toHtml() ?><span role="separator"> – </span><?= $item->end()->toHtml() ?>
</data>
