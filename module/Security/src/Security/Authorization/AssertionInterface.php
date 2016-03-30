<?php

namespace Security\Authorization;

use Zend\Permissions\Rbac\AssertionInterface as ZfAssert;
use Zend\Permissions\Rbac\RoleInterface;

/**
 * Interface AssertionInterface
 *
 * ${CARET}
 */
interface AssertionInterface extends ZfAssert
{
    /**
     * Sets the permission requested
     *
     * @param string $permission
     */
    public function setPermission($permission);

    /**
     * Sets role to use
     *
     * @param string|RoleInterface $role
     */
    public function setRole($role);
}
