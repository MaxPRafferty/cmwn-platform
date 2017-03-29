<?php

namespace Api\Links;

use Group\GroupInterface;
use ZF\Hal\Link\Link;

/**
 * hal link for address service
 */
class GroupAddressLink extends Link
{
    /**
     * GroupAddressLink constructor.
     * @param string $group
     */
    public function __construct($group)
    {
        parent::__construct('group_address');
        $groupId = $group instanceof GroupInterface ? $group->getGroupId() : $group;
        $this->setProps(['label' => 'Group Address']);
        $this->setRoute('api.rest.group-address', ['group_id' => $groupId], ['reuse_matched_params' => false]);
    }
}
