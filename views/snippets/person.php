<?php /** @var \Hks\Schema\Data\Person $item */ ?>

<div <?= attr([
    'class' => [
        'h-card',
        ...A::wrap($attrs['class'] ?? []),
    ],
    'itemscope' => true,
    'itemtype' => 'https://schema.org/Person',
    ...A::without($attrs ?? [], 'class'),
]) ?>>
    <div <?= attr([
        'class' => 'p-name',
        'itemprop' => 'name',
    ]) ?>>
        <?= esc($item->name()->displayName()) ?>
    </div>

    <?= $item->email()?->toHtml([
        'class' => 'u-email',
        'itemprop' => 'email',
    ]) ?>

    <?= $item->telephone()?->toHtml([
        'class' => 'p-tel',
        'itemprop' => 'telephone',
    ]) ?>

    <?= $item->fax()?->toHtml([
        'itemprop' => 'faxNumber',
    ]) ?>

    <?php if ($item->jobTitle() !== null) : ?>
        <div <?= attr([
            'class' => 'p-job-title',
            'itemprop' => 'jobTitle',
        ]) ?>>
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
