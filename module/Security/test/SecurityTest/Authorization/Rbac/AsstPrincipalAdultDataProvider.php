<?php


namespace SecurityTest\Authorization\Rbac;

/**
 * Class AsstPrincipalDataProvider
 * @package SecurityTest\Authorization
 */
class AsstPrincipalAdultDataProvider extends AbstractRoleDataProvider
{
    /**
     * AsstPrincipalAdultDataProvider constructor.
     * @param $rolesConfig
     */
    public function __construct($rolesConfig)
    {
        parent::__construct('asst_principal.adult', $rolesConfig);
        $this->setAllowed('add.group.user');
        $this->setAllowed('adult.code');
        $this->setAllowed('child.code');
        $this->setAllowed('create.child.group');
        $this->setAllowed('create.group');
        $this->setAllowed('edit.group');
        $this->setAllowed('edit.user.adult');
        $this->setAllowed('edit.user.child');
        $this->setAllowed('import');
        $this->setAllowed('remove.group.user');
        $this->setAllowed('remove.user.adult');
        $this->setAllowed('remove.user.child');
        $this->setAllowed('view.child.groups');
        $this->setAllowed('view.flip');
        $this->setAllowed('view.games');
        $this->setAllowed('view.group');
        $this->setAllowed('view.group.users');
        $this->setAllowed('view.org');
        $this->setAllowed('view.profile.image');
        $this->setAllowed('view.user.adult');
        $this->setAllowed('view.user.child');
        $this->setAllowed('view.user.flip');
        $this->setAllowed('view.user.groups');
        $this->setAllowed('view.user.orgs');
        $this->setAllowed('reset.group.code');
    }
}
