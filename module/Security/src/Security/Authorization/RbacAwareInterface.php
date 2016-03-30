<?php

namespace Security\Authorization;

/**
 * Interface RbacAwareInterface
 */
interface RbacAwareInterface
{
    /**
     * @param Rbac $rbac
     */
    public function setRbac(Rbac $rbac);

    /**
     * @return Rbac
     */
    public function getRbac();
}
