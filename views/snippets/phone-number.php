<?php /** @var \Hks\Schema\Data\Types\PhoneNumber $item */ ?>

<?= Html::tel($item->value(), attr: $attrs ?? []) ?>
