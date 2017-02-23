<?php

namespace IntegrationTest\Security\Rbac;

/**
 * Class StudentChildDataProvider
 *
 * @package SecurityTest\Authorization\Rbac
 */
class StudentChildDataProvider extends AbstractRoleDataProvider
{
    /**
     * StudentChildDataProvider constructor.
     *
     * @param $rolesConfig
     */
    public function __construct($rolesConfig)
    {
        parent::__construct('student.child', $rolesConfig);
        $this->setAllowed('can.friend');
        $this->setAllowed('view.flip');
        $this->setAllowed('view.games');
        $this->setAllowed('view.group');
        $this->setAllowed('view.user.groups');
        $this->setAllowed('view.group.users');
        $this->setAllowed('view.profile.image');
        $this->setAllowed('view.user.adult');
        $this->setAllowed('view.user.child');
        $this->setAllowed('view.user.flip');
        $this->setAllowed('view.address');
        $this->setAllowed('view.all.group.addresses');
    }
}
