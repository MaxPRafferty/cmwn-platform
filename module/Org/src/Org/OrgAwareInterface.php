<?php

namespace Org;

/**
 * An Object that is aware of an organization
 *
 * @deprecated
 */
interface OrgAwareInterface
{
    /**
     * @param $orgId
     */
    public function setOrgId($orgId);
}
