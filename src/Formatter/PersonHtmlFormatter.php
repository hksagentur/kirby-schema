<?php

namespace Hks\Schema\Formatter;

use Hks\Schema\Data\Person;
use Kirby\Toolkit\A;

/**
 * @extends Formatter<Person>
 */
class PersonHtmlFormatter extends Formatter
{
    public function defaults(): array
    {
        return [
            'format' => 'microformat',
        ];
    }

    public function format(mixed $person): string
    {
        $options = A::without($this->options(), ['format']);

        $formatter = match ($this->option('format')) {
            'microdata' => new PersonMicrodataFormatter($options),
            default => new PersonMicroformatFormatter($options),
        };

        return $formatter->format($person);
    }
}
