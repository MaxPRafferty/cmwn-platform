<?php

namespace Api\Links;

use Application\Utils\Type\StaticType;
use Group\GroupInterface;
use ZF\Hal\Link\Link;

/**
 * Class GroupUserLink
 */
class GroupUserLink extends Link
{
    /**
     * GroupUserLink constructor.
     * @param string $group
     */
    public function __construct($group)
    {
        $groupId = $group instanceof GroupInterface ? $group->getGroupId() : $group;
        $type    = $group instanceof GroupInterface ? $group->getType() : $group;
        try {
            $type = StaticType::getLabelForType($type);
        } catch (\InvalidArgumentException $invalidType) {
            $type = 'Group';
        }

        parent::__construct('group_users');
        $this->setProps(['label' => 'Users in ' . $type]);
        $this->setRoute('api.rest.group-users', ['group_id' => $groupId]);
    }
}
