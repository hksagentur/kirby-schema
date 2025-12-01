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

        return new Collection($data);
    },

];
