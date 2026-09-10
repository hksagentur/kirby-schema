<?php /** @var \Hks\Schema\Data\Link $item */ ?>

<a <?= attr([
    'href' => $item->url(),
    'rel' => $item->rel(),
    'target' => $item->target()->isDefault() ? null : $item->target()->value(),
    ...$item->isActive() ? ['aria-current' => 'page'] : [],
    ...$item->isOpen() ? ['data-current' => 'ancestor'] : [],
    ...$attrs ?? [],
]) ?>>
    <?= esc($item->title() ?? $item->url() ?? '') ?>
</a>
