<?php

namespace Security\Authorization\Assertion;

use Zend\Permissions\Rbac\RoleInterface;

/**
 * Trait to help satisfy AssertionInterface
 *
 * @see AssertionInterface
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
     * @param string[] $permission
     */
    public function setPermission(array $permission)
    {
        $this->permission = $permission;
    }

    /**
     * @param string|RoleInterface $role
     */
    public function setRole($role)
    {
        $this->role = $role;
    }
}
