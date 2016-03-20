<?php

namespace Api\Links;

use Group\GroupInterface;
use ZF\Hal\Link\Link;

/**
 * Class GoupUserLink
 */
class GroupUserLink extends Link
{
    public function __construct($group)
    {
        $groupId = $group instanceof GroupInterface ? $group->getGroupId() : $group;

        parent::__construct('group_users');
        $this->setRoute('api.rest.group-users', ['group_id' => $groupId]);
    }
}
