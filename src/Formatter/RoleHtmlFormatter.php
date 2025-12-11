<?php

namespace Hks\Schema\Formatter;

use Hks\Schema\Data\Role;

/**
 * @extends Formatter<Role>
 */
class RoleHtmlFormatter extends Formatter
{
    public function format(mixed $role): string
    {
        return (new RoleMicrodataFormatter(
            $this->options(),
        ))->format($role);
    }
}
