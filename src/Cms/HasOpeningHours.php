<?php

namespace Hks\Schema\Cms;

use DateTimeInterface;
use Hks\Schema\Data\OpeningHoursFactory;
use Spatie\OpeningHours\OpeningHours;

trait HasOpeningHours
{
    protected ?OpeningHours $openingHours = null;

    public function isOpen(): bool
    {
        return $this->openingHours()->isOpen();
    }

    public function isOpenOn(string $day): bool
    {
        return $this->openingHours()->isOpenOn($day);
    }

    public function isOpenAt(DateTimeInterface $date): bool
    {
        return $this->openingHours()->isOpenAt($date);
    }

    public function openingHours(): OpeningHours
    {
        return $this->openingHours ??= OpeningHoursFactory::createFromContent($this->hours()->toObject());
    }
}
