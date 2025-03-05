<?php

use Holab\Data\OpeningHoursFactory;
use Kirby\Content\Field;
use Spatie\OpeningHours\OpeningHours;

return [
    'toOpeningHours' => function (Field $field): OpeningHours {
        return OpeningHoursFactory::createFromContent($field->toObject());
    },
];
