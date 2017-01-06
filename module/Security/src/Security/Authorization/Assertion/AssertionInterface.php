<?php

namespace Security\Authorization\Assertion;

use Zend\Permissions\Rbac\AssertionInterface as ZfAssert;
use Zend\Permissions\Rbac\RoleInterface;

/**
 * Extended assertion that needs a permission set
 */
interface AssertionInterface extends ZfAssert
{
    /**
     * Sets the permissions requested
     *
     * @param string[] $permissions
     */
    public function setPermission(array $permissions);

    /**
     * Sets role to use
     *
     * @param string|RoleInterface $role
     */
    public function setRole($role);
}
