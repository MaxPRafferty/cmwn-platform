<?php

namespace Api\Links;

use Group\GroupInterface;
use ZF\Hal\Link\Link;

/**
 * Class GroupResetLink
 * @package Api\Links
 */
class GroupResetLink extends Link
{
    /**
     * GroupResetLink constructor.
     * @param string $group
     */
    public function __construct($group)
    {
        parent::__construct('group_reset');

        $groupId = $group instanceof GroupInterface ? $group->getGroupId() : $group;

        $this->setProps(['label' => 'Reset code for users in group']);
        $this->setRoute('api.rest.group-reset', ['group_id' => $groupId]);
    }
}
