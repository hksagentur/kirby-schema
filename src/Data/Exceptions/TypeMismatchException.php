<?php

namespace Hks\Schema\Data\Exceptions;

use UnexpectedValueException;

class TypeMismatchException extends UnexpectedValueException
{
    public static function for(string $message, mixed ...$values): self
    {
        return new self(sprintf($message, ...$values));
    }

    /** @param class-string $expected */
    public static function at(string $expected, mixed $actual, int|string $key): self
    {
        return new static(sprintf(
            'The value at index "%s" does not match the expected type "%s", got "%s".',
            $key,
            $expected,
            get_debug_type($actual)
        ));
    }

    public static function between(mixed $expected, mixed $actual): self
    {
        return new static(sprintf(
            'The value of type "%s" does not match the expected type "%s".',
            get_debug_type($actual),
            get_debug_type($expected),
        ));
    }
}
