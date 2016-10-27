<?php

namespace SecurityTest\Authorization\Rbac;

/**
 * Class NeighborChildDataProvider
 * @package SecurityTest\Authorization\Rbac
 */
class NeighborChildDataProvider extends AbstractRoleDataProvider
{
    /**
     * NeighborChildDataProvider constructor.
     * @param $rolesConfig
     */
    public function __construct($rolesConfig)
    {
        parent::__construct('neighbor.child', $rolesConfig);
        $this->setAllowed('can.friend');
        $this->setAllowed('child.code');
        $this->setAllowed('create.user.flip');
        $this->setAllowed('pick.username');
        $this->setAllowed('update.password');
        $this->setAllowed('view.flip');
        $this->setAllowed('view.games');
        $this->setAllowed('view.group');
        $this->setAllowed('view.group.users');
        $this->setAllowed('view.org');
        $this->setAllowed('view.org.users');
        $this->setAllowed('view.profile.image');
        $this->setAllowed('view.user.adult');
        $this->setAllowed('view.user.child');
        $this->setAllowed('view.user.flip');
    }
}
