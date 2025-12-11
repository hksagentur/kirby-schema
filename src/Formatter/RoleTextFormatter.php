<?php

namespace Hks\Schema\Formatter;

use Hks\Schema\Data\Role;

/**
 * @extends Formatter<Role>
 */
class RoleTextFormatter extends Formatter
{
    public function format(mixed $role): string
    {
        return $role->name()->toString();
    }
}
