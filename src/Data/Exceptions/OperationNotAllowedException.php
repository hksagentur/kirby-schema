<?php

namespace Hks\Schema\Data\Exceptions;

class OperationNotAllowedException extends \LogicException
{
    public static function for(string $message, mixed ...$values): self
    {
        return new static(sprintf($message, ...$values));
    }
}
