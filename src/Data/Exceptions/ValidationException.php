<?php

namespace Hks\Schema\Data\Exceptions;

class ValidationException extends \InvalidArgumentException
{
    public static function for(string $message, mixed ...$values): self
    {
        return new self(sprintf($message, ...$values));
    }
}
