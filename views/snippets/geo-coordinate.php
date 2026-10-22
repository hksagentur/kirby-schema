<?php /** @var \Hks\Schema\Data\Types\GeoCoordinate $item */ ?>

<a<?= attr([
    'href' => $item->toUri(),
    'class' => [
        'h-geo',
        ...A::wrap($attrs['class'] ?? []),
    ],
    'itemscope' => true,
    'itemtype' => 'https://schema.org/GeoCoordinates',
    ...A::without($attrs ?? [], 'class'),
], ' ') ?>>
    <data <?= attr([
        'value' => $item->latitude(),
        'class' => 'p-latitude',
        'itemprop' => 'latitude',
    ]) ?>>
        <?= esc(\Hks\Schema\Toolkit\Geo::formatLatitude($item->latitude())) ?>
    </data>

    <data <?= attr([
        'value' => $item->longitude(),
        'class' => 'p-longitude',
        'itemprop' => 'longitude',
    ]) ?>>
        <?= esc(\Hks\Schema\Toolkit\Geo::formatLongitude($item->longitude())) ?>
    </data>
</a>
