<?php

namespace Security\Authorization;

/**
 * Instance is aware of the Rbac
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
    public function getRbac(): Rbac;
}
