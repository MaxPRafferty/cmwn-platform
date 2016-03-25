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
     * @param string $parent
     * @todo add organization_id param
     */
    public function __construct($group, $parent = null, $orgId = null)
    {
        $type = $group instanceof GroupInterface ? $group->getType() : $group;
        parent::__construct(strtolower('group_' . $type));
        $query = ['type' => $type];

        if ($orgId !== null) {
            $query['org_id'] = $orgId;
        }

        if ($parent !== null) {
            $query['parent'] = $parent;
        }

        $this->setRoute('api.rest.group', [], ['query' => $query, 'reuse_matched_params' => false]);
    }
}
