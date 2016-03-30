<?php

namespace Security\Authorization;

use Zend\Permissions\Rbac\Exception\InvalidArgumentException;
use Zend\Permissions\Rbac\Rbac as ZfRbac;
use Zend\Permissions\Rbac\RoleInterface;

/**
 * Class Rbac
 */
class Rbac extends ZfRbac
{
    const SCOPE_REMOVE = 1;
    const SCOPE_UPDATE = 2;
    const SCOPE_CREATE = 4;

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
     * @param string|RoleInterface $objectOrName
     * @return RoleInterface
     */
    public function getRole($objectOrName)
    {
        try {
            return parent::getRole($objectOrName);
        } catch (InvalidArgumentException $notFound) {

        }

        return parent::getRole('guest');
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
//            $this->permissionBits[$roleName] = $this->permissionBits[$siblingRole];
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

        $parents = array_key_exists('parents', $roleConfig) ? $roleConfig['parents'] : [];
        $parents = !is_array($parents) ? [$parents] : $parents;
        $this->addRole($role, $parents);

        if (array_key_exists('permissions', $roleConfig)) {
            $this->addPermissionsToRole($role, $roleConfig['permissions']);
        }

        if (array_key_exists('entity_bits', $roleConfig)) {
            $this->permissionBits[$roleName] = $roleConfig['entity_bits'];
        }
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

            $defaults = [
                'permission' => null,
                'label'      => null,
            ];

            $permConfig = array_merge($defaults, $permConfig);
            $permission = $permConfig['permission'];
            $label      = $permConfig['label'] === null ? $permission : $permConfig['label'];

            if ($permission === null) {
                throw new \RuntimeException('Invalid Permission in config');
            }

            $role->addPermission($permission);
            $this->permissionLabels[$permission] = $label;
        }
    }

    /**
     * @param $role
     * @param $entity
     * @return int
     */
    public function getScopeForEntity($role, $entity)
    {
        $roleName = $role instanceof RoleInterface ? $role->getName() : $role;

        if (!array_key_exists($roleName, $this->permissionBits)) {
            return 0;
        }

        if (!array_key_exists($entity, $this->permissionBits[$roleName])) {
            return 0;
        }

        return $this->permissionBits[$roleName][$entity];
    }
}
