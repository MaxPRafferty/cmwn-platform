<?php

namespace IntegrationTest\Security\Rbac;

/**
 * Class SuperDataProvider
 *
 * @package SecurityTest\Authorization\Rbac
 */
class SuperDataProvider extends AbstractRoleDataProvider
{
    /**
     * SuperDataProvider constructor.
     *
     * @param $rolesConfig
     */
    public function __construct($rolesConfig)
    {
        parent::__construct('super', $rolesConfig);
        $this->setAllowed('add.group.user');
        $this->setAllowed('adult.code');
        $this->setAllowed('attach.profile.image');
        $this->setAllowed('child.code');
        $this->setAllowed('create.child.group');
        $this->setAllowed('create.group');
        $this->setAllowed('create.org');
        $this->setAllowed('create.user');
        $this->setAllowed('edit.group');
        $this->setAllowed('edit.org');
        $this->setAllowed('edit.user.adult');
        $this->setAllowed('edit.user.child');
        $this->setAllowed('import');
        $this->setAllowed('remove.child.group');
        $this->setAllowed('remove.group');
        $this->setAllowed('remove.group.user');
        $this->setAllowed('remove.org');
        $this->setAllowed('remove.user.adult');
        $this->setAllowed('remove.user.child');
        $this->setAllowed('skribble.notice');
        $this->setAllowed('update.password');
        $this->setAllowed('view.all.groups');
        $this->setAllowed('view.all.orgs');
        $this->setAllowed('view.all.users');
        $this->setAllowed('view.all.child.groups');
        $this->setAllowed('view.flip');
        $this->setAllowed('view.group');
        $this->setAllowed('view.group.users');
        $this->setAllowed('view.org');
        $this->setAllowed('view.org.users');
        $this->setAllowed('view.profile.image');
        $this->setAllowed('view.user.adult');
        $this->setAllowed('view.user.child');
        $this->setAllowed('view.user.flip');
        $this->setAllowed('view.user.groups');
        $this->setAllowed('view.user.orgs');
        $this->setAllowed('view.game-data');
        $this->setAllowed('flag.image');
        $this->setAllowed('view.all.flagged.images');
        $this->setAllowed('view.flagged.image');
        $this->setAllowed('delete.flag');
        $this->setAllowed('view.feed');
        $this->setAllowed('view.user.feed');
        $this->setAllowed('sa.settings');
        $this->setAllowed('create.game');
        $this->setAllowed('delete.game');
        $this->setAllowed('update.game');
        $this->setAllowed('view.game');
        $this->setAllowed('reset.group.code');
        $this->setAllowed('set.super');
        $this->setAllowed('get.super.user');
        $this->setAllowed('view.all.addresses');
        $this->setAllowed('view.address');
        $this->setAllowed('create.address');
        $this->setAllowed('update.address');
        $this->setAllowed('delete.address');
        $this->setAllowed('view.all.group.addresses');
        $this->setAllowed('attach.group.address');
        $this->setAllowed('detach.group.address');
        $this->setAllowed('view.deleted.games');
        $this->setAllowed('create.flip');
        $this->setAllowed('edit.flip');
        $this->setAllowed('delete.flip');
    }
}
