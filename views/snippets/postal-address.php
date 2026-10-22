<?php /** @var \Hks\Schema\Data\Types\PostalAddress $item */ ?>

<div <?= attr([
    'class' => [
        'h-adr',
        ...A::wrap($attrs['class'] ?? []),
    ],
    'itemscope' => true,
    'itemtype' => 'https://schema.org/PostalAddress',
    'translate' => 'no',
    ...A::without($attrs ?? [], 'class'),
]) ?>>
    <?= $item->addressLines(
        fn (string $value, string $field): string => Html::tag('span', $value, [
            'class' => match ($field) {
                'streetAddress' => 'p-street-address',
                'postalCode' => 'p-postal-code',
                'addressLocality' => 'p-locality',
                'addressRegion' => 'p-region',
                'addressCountry' => 'p-country-name',
                default => null,
            },
            'itemprop' => $field,
        ]),
    )->join("<br>\n") ?>
</div>
