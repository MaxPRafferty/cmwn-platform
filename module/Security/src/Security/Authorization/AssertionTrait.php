<?php

namespace Security\Authorization;

use Zend\Permissions\Rbac\RoleInterface;

/**
 * Class AssertionTrait
 */
trait AssertionTrait
{
    /**
     * @var string[]
     */
    protected $permission;

    /**
     * @var string|RoleInterface
     */
    protected $role;

    /**
     * @param $permission
     */
    public function setPermission(array $permission)
    {
        $this->permission = $permission;
    }

    /**
     * @param $role
     */
    public function setRole($role)
    {
        $this->role = $role;
    }
}
