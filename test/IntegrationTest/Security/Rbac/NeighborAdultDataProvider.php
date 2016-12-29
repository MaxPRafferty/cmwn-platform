<?php

namespace IntegrationTest\Security\Rbac;

/**
 * Class NeighborAdultDataProvider
 *
 * @package SecurityTest\Authorization\Rbac
 */
class NeighborAdultDataProvider extends AbstractRoleDataProvider
{
    /**
     * NeighborAdultDataProvider constructor.
     *
     * @param $rolesConfig
     */
    public function __construct($rolesConfig)
    {
        parent::__construct('neighbor.adult', $rolesConfig);
        $this->setAllowed('view.flip');
        $this->setAllowed('view.profile.image');
        $this->setAllowed('view.user.adult');
        $this->setAllowed('view.user.flip');
    }
}
