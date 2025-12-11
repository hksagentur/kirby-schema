<?php

namespace Hks\Schema\Formatter;

use Hks\Site\Toolkit\Str;
use InvalidArgumentException;
use UnexpectedValueException;

class Manager
{
    protected static string $namespace = 'Hks\\Schema\\Formatter';
    protected static ?self $instance = null;

    public static function instance(self|null $instance = null): ?static
    {
        if ($instance !== null) {
            return static::$instance = $instance;
        }

        return static::$instance ?? new static();
    }

    /**
     * @template TFormatter of Formatter
     *
     * @param string|class-string<TFormatter> $formatter
     * @param mixed $value
     * @param array $options
     * @return string
     */
    public function format(string $formatter, mixed $value, array $options = []): string
    {
        $class = ! class_exists($formatter)
            ? static::resolveFormatterName($value, $formatter)
            : $formatter;

        $instance = new $class($options);

        if (! ($instance instanceof Formatter)) {
            throw new UnexpectedValueException(sprintf('Formatter must extend "%s"', Formatter::class));
        }

        return $instance->format($value);
    }

    /**
     * @template TValue of object
     *
     * @param class-string<TValue>|TValue $value
     * @param string $format
     * @return class-string<Formatter<TValue>>
     */
    public static function resolveFormatterName(string $value, string $format): string
    {
        $class = static::$namespace . Str::classBasename($value) . Str::studly($format) . 'Formatter';

        if (! class_exists($class)) {
            throw new InvalidArgumentException(sprintf(
                'Could not find a suitable formatter for the given value type "%s"',
                Str::classBasename($value),
            ));
        }

        return $class;
    }
}
