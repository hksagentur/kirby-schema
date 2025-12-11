<?php

namespace Hks\Schema\Formatter;

use Hks\Schema\Data\DataCollection;
use Hks\Schema\Data\Person;
use Hks\Schema\Toolkit\I18n;
use Kirby\Cms\Html;

/**
 * @extends MicroformatFormatter<Person>
 */
class PersonMicroformatFormatter extends MicroformatFormatter
{
    public function getMicroformatType(): string
    {
        return 'h-card';
    }

    public function getAttributes(mixed $person): array
    {
        return [
            'image' => fn () => $person->image()?->toHtml(['class' => 'u-photo']),
            'name' => fn () => $person->fullName()?->toHtml(['class' => 'p-name']),
            'jobTitle' => fn () => $person->roles()?->unlessEmpty(fn (DataCollection $roles) => Html::tag('span', [
                I18n::oxfordList($roles->pluck('name')),
            ], [
                'class' => 'p-job-title',
            ])),
            'telephone' => fn () => $person->telephone()?->toHtml(['class' => 'p-tel']),
            'fax' => fn () => $person->fax()?->toHtml(['class' => 'p-tel-fax']),
            'email' => fn () => $person->email()?->toHtml(['class' => 'u-email']),
        ];
    }
}
