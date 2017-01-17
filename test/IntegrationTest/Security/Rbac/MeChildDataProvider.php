<?php

namespace IntegrationTest\Security\Rbac;

/**
 * Class MeChildDataProvider
 *
 * @package SecurityTest\Authorization\Rbac
 */
class MeChildDataProvider extends AbstractRoleDataProvider
{
    /**
     * MeChildDataProvider constructor.
     *
     * @param $rolesConfig
     */
    public function __construct($rolesConfig)
    {
        parent::__construct('me.child', $rolesConfig);
        $this->setAllowed('attach.profile.image');
        $this->setAllowed('can.friend');
        $this->setAllowed('create.skribble');
        $this->setAllowed('create.user.flip');
        $this->setAllowed('delete.skribble');
        $this->setAllowed('edit.user.child');
        $this->setAllowed('pick.username');
        $this->setAllowed('save.game');
        $this->setAllowed('update.password');
        $this->setAllowed('update.skribble');
        $this->setAllowed('view.flip');
        $this->setAllowed('view.games');
        $this->setAllowed('view.profile.image');
        $this->setAllowed('view.skribble');
        $this->setAllowed('view.user.child');
        $this->setAllowed('view.user.flip');
        $this->setAllowed('view.user.groups');
        $this->setAllowed('view.feed');
        $this->setAllowed('flag.image');
    }
}
