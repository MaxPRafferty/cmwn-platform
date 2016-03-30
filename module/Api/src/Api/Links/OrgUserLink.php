<?php

namespace Api\Links;

use Org\OrganizationInterface;
use ZF\Hal\Link\Link;

/**
 * Class OrgUserLink
 */
class OrgUserLink extends Link
{
    public function __construct($org)
    {
        $orgId = $org instanceof OrganizationInterface ? $org->getOrgId() : $org;

        parent::__construct('org_users');
        $this->setRoute('api.rest.org-users', ['org_id' => $orgId]);
    }
}
