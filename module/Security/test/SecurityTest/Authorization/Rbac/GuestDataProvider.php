<?php


namespace SecurityTest\Authorization\Rbac;

/**
 * Class GuestDataProvider
 * @package SecurityTest\Authorization\Rbac
 */
class GuestDataProvider extends AbstractRoleDataProvider
{
    /**
     * GuestDataProvider constructor.
     * @param $rolesConfig
     */
    public function __construct($rolesConfig)
    {
        parent::__construct('guest', $rolesConfig);
    }
}
