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
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->permissionLabels = $config['permission_labels'];
        array_walk($config['roles'], [$this, 'addRoleFromConfig']);
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
     * Creates a role from a config
     *
     * @param $roleConfig
     * @param $roleName
     */
    public function addRoleFromConfig($roleConfig, $roleName)
    {
        $default = [
            'permissions' => [],
            'entity_bits' => [],
        ];

        $roleConfig = array_merge($default, $roleConfig);

        $role    = new Role($roleName);
        $parents = array_key_exists('parents', $roleConfig) ? $roleConfig['parents'] : [];
        $parents = !is_array($parents) ? [$parents] : $parents;
        $this->addRole($role, $parents);
        foreach ($roleConfig['permissions'] as $permission) {
            $role->addPermission($permission);
        }

        $this->permissionBits[$roleName] = $roleConfig['entity_bits'];
    }

    /**
     * Gets the label for a permission
     *
     * @param $permission
     * @return string
     */
    public function getLabelForPermission($permission)
    {
        if (!isset($this->permissionLabels[$permission])) {
            throw new \InvalidArgumentException('Invalid permission: ' . $permission);
        }

        return $this->permissionLabels[$permission];
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

    /**
     * @return array|mixed
     */
    public function getPermissions()
    {
        return $this->permissionLabels;
    }

    /**
     * @return array
     */
    public function getConfiguredScopes()
    {
        $allEntities = array_map(function ($role) {
            return array_keys($this->permissionBits[$role]);
        }, $this->permissionBits);

        sort($allEntities);
        array_unique($allEntities);

        return $allEntities;
    }
}
