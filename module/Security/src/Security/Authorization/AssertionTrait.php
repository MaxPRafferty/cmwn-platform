<?php

namespace Security\Authorization;

use Zend\Permissions\Rbac\RoleInterface;

/**
 * Class AssertionTrait
 *
 * ${CARET}
 */
trait AssertionTrait
{
    /**
     * @var string
     */
    protected $permission;

    /**
     * @var string|RoleInterface
     */
    protected $role;

    /**
     * @param $permission
     */
    public function setPermission($permission)
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
