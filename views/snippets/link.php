<?php /** @var \Hks\Schema\Data\Link $item */ ?>

<?php if ($url = $item->url()) : ?>
    <a <?= attr([
        'href' => $url,
        'rel' => $item->rel(),
        'target' => $item->target()->isDefault() ? null : $item->target()->value(),
        ...$item->isActive() ? ['aria-current' => 'page'] : [],
        ...$item->isOpen() ? ['data-current' => 'ancestor'] : [],
        ...$attrs ?? [],
    ]) ?>>
        <?= esc($item->text() ?? $url) ?>
    </a>
<?php endif ?>
