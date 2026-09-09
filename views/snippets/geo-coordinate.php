<?php /** @var \Hks\Schema\Data\Types\GeoCoordinate $item */ ?>

<a<?= attr([
    'href' => $item->toUri(),
    'itemscope' => true,
    'itemtype' => 'https://schema.org/GeoCoordinates',
    ...$attrs ?? [],
], ' ') ?>>
    <data <?= attr([
        'value' => $item->latitude(),
        'itemprop' => 'latitude',
    ]) ?>>
        <?= esc(\Hks\Schema\Toolkit\Geo::formatLatitude($item->latitude())) ?>
    </data>

    <data <?= attr([
        'value' => $item->longitude(),
        'itemprop' => 'longitude',
    ]) ?>>
        <?= esc(\Hks\Schema\Toolkit\Geo::formatLongitude($item->longitude())) ?>
    </data>
</a>
