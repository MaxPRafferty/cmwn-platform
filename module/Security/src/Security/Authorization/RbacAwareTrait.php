<?php

namespace Security\Authorization;

/**
 * Trait RbacAwareTrait
 *
 * ${CARET}
 */
trait RbacAwareTrait
{
    /**
     * @var Rbac
     */
    protected $rbac;

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
