<?php

namespace Api\Links;

use Application\Utils\StaticType;
use Group\GroupInterface;
use Org\OrganizationInterface;
use ZF\Hal\Link\Link;

/**
 * Class GroupLink
 *
 * @package Api\Links
 */
class GroupLink extends Link
{
    /**
     * GroupLink constructor.
     *
     * @param string $group
     * @param $options
     */
    public function __construct($group = null, ...$options)
    {
        $query = [];

        array_walk($options, function ($option) use (&$query) {
            switch (true) {
                case $option instanceof GroupInterface:
                    $query['parent'] = $option->getGroupId();
                    break;

                case $option instanceof OrganizationInterface:
                    $query['org_id'] = $option->getOrgId();
                    break;

                default:
                    // Nothing to see here
            }
        });

        $type  = $group instanceof GroupInterface ? $group->getType() : $group;
        $label = 'group';
        if (!empty($type)) {
            $label .= '_' . $type;
            $query['type'] = $type;
            $this->setProps(['label' => StaticType::getLabelForType($type)]);
        }

        parent::__construct(strtolower($label));
        $this->setRoute('api.rest.group', [], ['query' => $query, 'reuse_matched_params' => false]);
    }
}
