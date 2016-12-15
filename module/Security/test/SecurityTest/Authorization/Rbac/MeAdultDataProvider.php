<?php


namespace SecurityTest\Authorization\Rbac;

/**
 * Class MeAdultDataProvider
 * @package SecurityTest\Authorization\Rbac
 */
class MeAdultDataProvider extends AbstractRoleDataProvider
{
    /**
     * MeAdultDataProvider constructor.
     * @param $rolesConfig
     */
    public function __construct($rolesConfig)
    {
        parent::__construct('me.adult', $rolesConfig);
        $this->setAllowed('attach.profile.image');
        $this->setAllowed('edit.user.adult');
        $this->setAllowed('update.password');
        $this->setAllowed('view.flip');
        $this->setAllowed('view.games');
        $this->setAllowed('view.profile.image');
        $this->setAllowed('view.user.adult');
        $this->setAllowed('view.user.flip');
        $this->setAllowed('view.user.groups');
        $this->setAllowed('view.user.orgs');
        $this->setAllowed('view.user.feed');
        $this->setAllowed('flag.image');
    }
}
