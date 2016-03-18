<?php

namespace Security\Authorization;

use Zend\Permissions\Rbac\Role as ZfRole;
use Zend\Permissions\Rbac\RoleInterface;

/**
 * Class Role
 *
 * ${CARET}
 */
class Role extends ZfRole
{
    /**
     * Copies permissions to a role
     *
     * @param RoleInterface $role
     */
    public function copyPermissionToRole(RoleInterface $role)
    {
        foreach (array_keys($this->permissions) as $permission) {
            $role->addPermission($permission);
        }
    }
}
