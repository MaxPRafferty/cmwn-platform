<?php

namespace Api\Links;

use Application\Utils\Type\StaticType;
use Org\OrganizationInterface;
use ZF\Hal\Link\Link;

/**
 * Class OrgLink
 * @package Api\Links
 */
class OrgLink extends Link
{
    /**
     * OrgLink constructor.
     * @param string $org
     */
    public function __construct($org = null)
    {
        if (!$org) {
            parent::__construct('org');
            $this->setProps(['label' => 'org']);
            $this->setRoute('api.rest.org');
            return;
        }

        $type = $org instanceof OrganizationInterface ? $org->getType() : $org;
        parent::__construct(strtolower('org_' . $type));
        $this->setProps(['label' => StaticType::getLabelForType($type)]);
        $this->setRoute('api.rest.org', [], ['query' => ['type' => $type]]);
    }
}
