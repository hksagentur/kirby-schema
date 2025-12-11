<?php

namespace Hks\Schema\Formatter;

use Closure;
use UnexpectedValueException;

/**
 * @template TValue
 * @extends Formatter<TValue>
 */
abstract class StructuredDataFormatter extends Formatter
{
    /**
     * @param TValue $object
     * @return array<string, Closure>
     */
    abstract public function getAttributes(mixed $object): array;

    /**
     * @param string[] $content
     * @return string
     */
    abstract public function compose(array $content): string;

    public function defaults(): array
    {
        return array_merge(parent::defaults(), [
            'include' => ['*'],
            'exclude' => [],
        ]);
    }

    public function format(mixed $object): string
    {
        $formatters = $this->getAttributes($object);

        $include = $this->option('include');
        $exclude = $this->option('exclude');

        if ($include === ['*']) {
            $include = array_keys($formatters);
        }

        $attributes = array_diff($include, $exclude);

        if (empty($attributes)) {
            return '';
        }

        $content = [];

        foreach ($attributes as $attribute) {
            $formatter = $formatters[$attribute] ?? null;

            if (! ($formatter instanceof Closure)) {
                throw new UnexpectedValueException(sprintf(
                    'Missing or invalid formatter definition for attribute "%s" in "%s"',
                    $attribute,
                    static::class,
                ));
            }

            $result = $formatter();

            if (! blank($result)) {
                $content[$attribute] = $result;
            }
        }

        if (empty($content)) {
            return '';
        }

        return $this->compose($content);
    }
}
