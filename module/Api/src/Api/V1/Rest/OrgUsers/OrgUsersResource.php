<?php
namespace Api\V1\Rest\OrgUsers;

use Api\V1\Rest\User\UserEntity;
use Group\Service\UserGroupServiceInterface;
use Org\Service\OrganizationServiceInterface;
use ZF\Rest\AbstractResourceListener;

/**
 * Class OrgUsersResource
 *
 * @package Api\V1\Rest\OrgUsers
 */
class OrgUsersResource extends AbstractResourceListener
{
    /**
     * @var UserGroupServiceInterface
     */
    protected $groupService;

    /**
     * @var OrganizationServiceInterface
     */
    protected $orgService;

    /**
     * OrgUsersResource constructor.
     *
     * @param UserGroupServiceInterface $groupService
     * @param OrganizationServiceInterface $orgService
     */
    public function __construct(UserGroupServiceInterface $groupService, OrganizationServiceInterface $orgService)
    {
        $this->groupService = $groupService;
        $this->orgService   = $orgService;
    }

    /**
     * @inheritdoc
     */
    public function fetchAll($params = [])
    {
        $orgId = $this->getEvent()->getRouteParam('org_id', false);

        $organization = $this->orgService->fetchOrganization($orgId);
        return new OrgUsersCollection($this->groupService->fetchUsersForOrg($organization, [], new UserEntity()));
    }
}
