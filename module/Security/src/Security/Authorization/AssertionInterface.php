<?php

namespace Security\Authorization;

use Zend\Permissions\Rbac\AssertionInterface as ZfAssert;
use Zend\Permissions\Rbac\RoleInterface;

/**
 * Interface AssertionInterface
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
