<?php /** @var \Hks\Schema\Data\Person $item */ ?>

<div <?= attr([
    'itemscope' => true,
    'itemtype' => 'https://schema.org/Person',
    ...$attrs ?? [],
]) ?>>
    <div <?= attr([
        'itemprop' => 'name',
    ]) ?>>
        <?= esc($item->name()->displayName()) ?>
    </div>

    <?= $item->email()?->toHtml([
        'itemprop' => 'email',
    ]) ?>

    <?= $item->telephone()?->toHtml([
        'itemprop' => 'telephone',
    ]) ?>

    <?= $item->fax()?->toHtml([
        'itemprop' => 'faxNumber',
    ]) ?>

    <?php if ($item->jobTitle() !== null) : ?>
        <div <?= attr(['itemprop' => 'jobTitle']) ?>>
            <?= esc($item->jobTitle()) ?>
        </div>
    <?php endif ?>

    <?php foreach ($item->qualifications() as $qualification) : ?>
        <span <?= attr([
            'itemprop' => 'hasCredential',
            'itemscope' => true,
            'itemtype' => 'https://schema.org/EducationalOccupationalCredential',
        ]) ?>>
            <span <?= attr(['itemprop' => 'name']) ?>><?= esc($qualification) ?></span>
        </span>
    <?php endforeach ?>

    <?php foreach ($item->expertise() as $topic) : ?>
        <meta itemprop="knowsAbout" content="<?= esc($topic) ?>">
    <?php endforeach ?>
</div>
