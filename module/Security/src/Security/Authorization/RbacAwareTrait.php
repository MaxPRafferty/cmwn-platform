<?php

namespace Security\Authorization;

/**
 * Trait RbacAwareTrait
 *
 * Trait to help a class be aware of the RBAC
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
    public function getRbac()
    {
        return $this->rbac;
    }
}
