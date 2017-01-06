<?php

namespace Security\Authorization;

/**
 * Helps satisfiy RbacAwareInterface
 *
 * @see RbacAwareInterface
 */
trait RbacAwareTrait
{
    /**
     * @var Rbac
     */
    protected $rbac;

    /**
     * @param Rbac $rbac
     */
    public function setRbac(Rbac $rbac)
    {
        $this->rbac = $rbac;
    }

    /**
     * @return Rbac
     */
    public function getRbac(): Rbac
    {
        return $this->rbac;
    }
}
