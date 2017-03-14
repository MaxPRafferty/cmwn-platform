<?php

namespace Api\Links;

use Application\Utils\Type\StaticType;
use Org\OrganizationInterface;
use ZF\Hal\Link\Link;

/**
 * Class OrgUserLink
 */
class OrgUserLink extends Link
{
    /**
     * OrgUserLink constructor.
     * @param string $org
     */
    public function __construct($org)
    {
        $orgId = $org instanceof OrganizationInterface ? $org->getOrgId() : $org;
        $type  = $org instanceof OrganizationInterface ? $org->getType() : $org;
        try {
            $type = StaticType::getLabelForType($type);
        } catch (\InvalidArgumentException $invalidType) {
            $type = 'Organization';
        }

        parent::__construct('org_users');
        $this->setProps(['label' => 'Users in ' . $type]);
        $this->setRoute('api.rest.org-users', ['org_id' => $orgId]);
    }
}
