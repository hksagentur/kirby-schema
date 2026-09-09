<?php /** @var \Hks\Schema\Data\Types\Fraction $item */ ?>

<span <?= attr([
    'role' => 'math',
    ...$attrs ?? [],
]) ?>>
    <?php if ($item->hasDenominator(1)) : ?>
        <?= $item->numerator() ?>
    <?php else : ?>
       <?= $item->numerator() ?> ⁄<?= $item->denominator() ?>
    <?php endif ?>
</span>
