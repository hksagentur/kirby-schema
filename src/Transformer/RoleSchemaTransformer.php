<?php

namespace Hks\Schema\Transformer;

use Hks\Schema\Data\Role;
use Hks\Schema\Toolkit\I18n;

/**
 * @extends Transformer<Role>
 */
class RoleSchemaTransformer extends Transformer
{
    public function transform(object $role): array
    {
        return array_filter([
            '@type' => 'Occuapation',
            'name' => $role->name()->value(),
            'responsibilities' => I18n::oxfordList($role->responsibilities()->pluck('name')),
        ]);
    }
}
