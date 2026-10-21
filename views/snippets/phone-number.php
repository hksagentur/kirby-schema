<?php /** @var \Hks\Schema\Data\Types\PhoneNumber $item */ ?>

<?= Html::tel($item->toE164String(), $item->value(), attr: $attrs ?? []) ?>
