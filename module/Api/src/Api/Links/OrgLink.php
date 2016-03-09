<?php

namespace Api\Links;

use Org\OrganizationInterface;
use ZF\Hal\Link\Link;

/**
 * Class OrgLink
 * @package Api\Links
 */
class OrgLink extends Link
{
    public function __construct(OrganizationInterface $org, $scope = null)
    {
        parent::__construct(strtolower('org_' . $org->getType()));
        $this->setRoute('api.rest.org', [], ['query' => ['type' => $org->getType()]]);

        // TODO get the label for this type
        $props = ['label' => 'My ' . $org->getType()];
        if (null !== $scope) {
            $props['scope'] = (int) $scope;
        }

        $this->setProps($props);
    }
}
