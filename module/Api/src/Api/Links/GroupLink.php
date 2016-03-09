<?php

namespace Api\Links;

use Group\GroupInterface;
use ZF\Hal\Link\Link;

/**
 * Class GroupLink
 * @package Api\Links
 */
class GroupLink extends Link
{
    /**
     * GroupLink constructor.
     * @param string $group
     */
    public function __construct($group)
    {
        $type = $group instanceof GroupInterface ? $group->getType() : $group;

        parent::__construct(strtolower('group_' . strtolower($type)));
        $this->setRoute('api.rest.group', [], ['query' => ['type' => $type]]);
    }
}
