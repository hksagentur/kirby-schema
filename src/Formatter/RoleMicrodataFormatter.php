<?php

namespace Hks\Schema\Formatter;

use Hks\Schema\Data\DataCollection;
use Hks\Schema\Data\Role;
use Hks\Schema\Toolkit\I18n;
use Kirby\Cms\Html;

/**
 * @extends MicrodataFormatter<Role>
 */
class RoleMicrodataFormatter extends MicrodataFormatter
{
    public function getSchemaType(): string
    {
        return 'https://schema.org/Occupation';
    }

    public function getAttributes(mixed $role): array
    {
        return [
            'name' => fn () => $role->name()->toHtml(['itemprop' => 'name']),
            'responsibilities' => fn () => $role->responsibilities()->unlessEmpty(fn (DataCollection $terms) => Html::tag('meta', attr: [
                'itemprop' => 'responsibilities',
                'content' => I18n::oxfordList($terms->pluck('name')),
            ])),
        ];
    }
}
