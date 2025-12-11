<?php

namespace Hks\Schema\Formatter;

use Hks\Schema\Data\DataCollection;
use Hks\Schema\Data\Person;
use Hks\Schema\Toolkit\I18n;
use Kirby\Cms\Html;

/**
 * @extends MicrodataFormatter<Person>
 */
class PersonMicrodataFormatter extends MicrodataFormatter
{
    public function getSchemaType(): string
    {
        return 'https://schema.org/Person';
    }

    public function getAttributes(mixed $person): array
    {
        return [
            'image' => fn () => $person->image()?->toHtml(['itemprop' => 'image']),
            'name' => fn () => $person->fullName()?->toHtml(['itemprop' => 'name']),
            'jobTitle' => fn () => $person->roles()?->unlessEmpty(fn (DataCollection $roles) => Html::tag('span', [
                I18n::oxfordList($roles->pluck('name')),
            ], [
                'itemprop' => 'jobTitle',
            ])),
            'telephone' => fn () => $person->telephone()?->toHtml(['itemprop' => 'telephone']),
            'fax' => fn () => $person->fax()?->toHtml(['itemprop' => 'faxNumber']),
            'email' => fn () => $person->email()?->toHtml(['itemprop' => 'email']),
        ];
    }
}
