<?php

namespace Security\Authorization\Assertion;

use Zend\Permissions\Rbac\Rbac;

/**
 * Since we always pass in assertion functions, this just checks the permission
 */
class DefaultAssertion implements AssertionInterface
{
    use AssertionTrait;

    /**
     * Assertion method - must return a boolean.
     *
     * @param  Rbac $rbac
     * @return bool
     */
    public function assert(Rbac $rbac)
    {
        foreach ($this->permission as $permission) {
            if ($rbac->isGranted($this->role, $permission)) {
                return true;
            }
        }

        return false;
    }
}
