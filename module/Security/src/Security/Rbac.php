<?php

namespace Security;

use Zend\Permissions\Rbac\Rbac as ZfRbac;
use Zend\Permissions\Rbac\RoleInterface;

/**
 * Class Rbac
 */
class Rbac extends ZfRbac
{
    const SCOPE_CREATE = 2;
    const SCOPE_UPDATE = 4;
    const SCOPE_REMOVE = 8;

    /**
     * @var array
     */
    protected $permissionLabels = [];

    /**
     * @var array
     */
    protected $permissionBits = [];

    /**
     * Rbac constructor.
     * @param array $roles
     */
    public function __construct(array $roles)
    {
        array_walk($roles, [$this, 'addRoleFromConfig']);
        array_walk($roles, [$this, 'copyPermissionsFromSibling']);
    }

    /**
     * Copies the permissions from each sibling role
     *
     * @param $roleConfig
     * @param $roleName
     */
    public function copyPermissionsFromSibling($roleConfig, $roleName)
    {
        $role     = $this->getRole($roleName);
        $siblings = array_key_exists('siblings', $roleConfig) ? $roleConfig['siblings'] : [];
        $siblings = !is_array($siblings) ? [$siblings] : $siblings;
        foreach ($siblings as $siblingRole) {
            /** @var Role $sibling */
            $sibling = $this->getRole($siblingRole);
            $sibling->copyPermissionToRole($role);
        }
    }

    /**
     * Creates a role from a config
     *
     * @param $roleConfig
     * @param $roleName
     */
    public function addRoleFromConfig($roleConfig, $roleName)
    {
        $role = new Role($roleName);
        if (array_key_exists('permissions', $roleConfig)) {
            $this->addPermissionsToRole($role, $roleConfig['permissions']);
        }

        $parents = array_key_exists('parents', $roleConfig) ? $roleConfig['parents'] : [];
        $parents = !is_array($parents) ? [$parents] : $parents;
        $this->addRole($role, $parents);
    }

    /**
     * Adds permissions to a role from a config and attaches a lable to a role
     *
     * @param RoleInterface $role
     * @param array $permissions
     */
    public function addPermissionsToRole(RoleInterface $role, array $permissions)
    {
        foreach ($permissions as $permConfig) {
            if (!is_array($permConfig)) {
                $role->addPermission($permConfig);
                continue;
            }

            $permission = array_key_exists('permission', $permConfig) ? $permConfig['permission'] : null;
            $label      = array_key_exists('label', $permConfig) ? $permConfig['label'] : $permission;

            if ($permission === null) {
                throw new \RuntimeException('Invalid Permission in config');
            }

            $role->addPermission($permission);
            $this->permissionLabels[$permission] = $label;
        }
    }
}
