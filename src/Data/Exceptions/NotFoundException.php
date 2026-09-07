<?php

namespace Hks\Schema\Data\Exceptions;

class NotFoundException extends \OutOfBoundsException
{
    public static function for(string $message, mixed ...$values): self
    {
        return new static(sprintf($message, ...$values));
    }
}
