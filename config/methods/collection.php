<?php

use Kirby\Cms\Collection;

return [

    /**
     * Run an associative map over each of the items.
     */
    'mapWithKeys' => function (callable $callback): Collection {
        /** @var Collection $this */
        $data = [];

        foreach ($this->data() as $key => $value) {
            $map = $callback($value, $key);

            foreach ($map as $mapKey => $mapValue) {
                $data[$mapKey] = $mapValue;
            }
        }

        // The constructor appends plain values with numeric keys,
        // so the mapped keys have to be set as the data directly.
        return (new Collection())->data($data);
    },

];
