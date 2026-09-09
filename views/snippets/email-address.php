<?php /** @var \Hks\Schema\Data\Types\EmailAddress $item */ ?>

<?= Html::email($item->value(), attr: $attrs ?? []) ?>
