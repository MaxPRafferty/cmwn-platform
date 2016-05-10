<?php

namespace Api\Links;

use Group\GroupInterface;
use ZF\Hal\Link\Link;

/**
 * Class GroupLink
 */
class ImportLink extends Link
{
    /**
     * GroupLink constructor.
     * @param string $group
     */
    public function __construct($group)
    {
        $groupId = $group instanceof GroupInterface ? $group->getGroupId() : $group;

        parent::__construct('import');
        $this->setProps(['label' => 'Import Users']);
        $this->setRoute('api.rest.import', ['group_id' => $groupId]);
    }
}
