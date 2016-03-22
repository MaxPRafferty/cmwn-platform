<?php

namespace Security\Authorization\Assertions;

use Security\Authorization\AssertionInterface;
use Security\Authorization\AssertionTrait;
use Zend\Permissions\Rbac\Rbac;

/**
 * Class DefaultAssertion
 *
 * ${CARET}
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
        return $rbac->isGranted($this->role, $this->permission);
    }
}
