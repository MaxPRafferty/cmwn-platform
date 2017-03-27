<?php

namespace IntegrationTest\Security\Rbac;

/**
 * Class TeacherDataProvider
 *
 * @package SecurityTest\Authorization\Rbac
 */
class TeacherAdultDataProvider extends AbstractRoleDataProvider
{
    /**
     * TeacherAdultDataProvider constructor.
     *
     * @param $rolesConfig
     */
    public function __construct($rolesConfig)
    {
        parent::__construct('teacher.adult', $rolesConfig);
        $this->setAllowed('child.code');
        $this->setAllowed('edit.group');
        $this->setAllowed('edit.user.child');
        $this->setAllowed('remove.user.child');
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
        $this->setAllowed('view.address');
        $this->setAllowed('create.address');
        $this->setAllowed('update.address');
        $this->setAllowed('delete.address');
        $this->setAllowed('view.all.group.addresses');
        $this->setAllowed('attach.group.address');
        $this->setAllowed('detach.group.address');
    }
}
