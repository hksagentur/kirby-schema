<?php

namespace Hks\Schema\Formatter;

use Hks\Schema\Data\Person;
use Hks\Schema\Toolkit\I18n;

/**
 * @extends Formatter<Person>
 */
class PersonTextFormatter extends StructuredDataFormatter
{
    public function getAttributes(mixed $person): array
    {
        return [
            'name' => fn () => $person->fullName()?->toString(),
            'email' => fn () => $person->email()?->toString(),
            'telephone' => fn () => $person->telephone()?->toString(),
            'fax' => fn () => $person->fax()?->toString(),
            'jobTitle' => fn () => I18n::oxfordList($person->roles()->pluck('name')),
        ];
    }

    public function compose(array $content): string
    {
        return I18n::template('hksagentur.schema.formatters.person', replace: $content);
    }
}
