<?php

namespace Hks\Schema\Transformer;

use Hks\Schema\Data\Person;
use Hks\Schema\Data\Role;
use Hks\Schema\Toolkit\I18n;

/**
 * @extends Transformer<Person>
 */
class PersonSchemaTransformer extends Transformer
{
    public function transform(object $person): array
    {
        return array_filter([
            '@type' => 'Person',
            'name' => $person->fullName()->toString(),
            'givenName' => $person->givenName()?->toString(),
            'additionalName' => $person->additionalName()?->toString(),
            'familyName' => $person->familyName()?->toString(),
            'image' => $person->image()?->toString(),
            'disambiguatingDescription' => $person->description()?->toString(),
            'jobTitle' => I18n::oxfordList($person->roles()->pluck('name')),
            'email' => $person->email()?->toString(),
            'telephone' => $person->telephone()?->toString(),
            'faxNumber' => $person->fax()?->toString(),
            'hasOccupation' => $person->roles()->toArray(fn (Role $role) => $role->toSchema()),
        ]);
    }
}
