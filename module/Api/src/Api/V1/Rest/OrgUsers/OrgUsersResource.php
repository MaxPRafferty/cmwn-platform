<?php
namespace Api\V1\Rest\OrgUsers;

use Api\V1\Rest\User\UserEntity;
use Group\Service\UserGroupServiceInterface;
use ZF\ApiProblem\ApiProblem;
use ZF\Rest\AbstractResourceListener;

/**
 * Class OrgUsersResource
 * @package Api\V1\Rest\OrgUsers
 */
class OrgUsersResource extends AbstractResourceListener
{
    /**
     * @var UserGroupServiceInterface
     */
    protected $groupService;

    /**
     * OrgUsersResource constructor.
     * @param UserGroupServiceInterface $groupService
     */
    public function __construct(UserGroupServiceInterface $groupService)
    {
        $this->groupService = $groupService;
    }

    /**
     * Fetch a resource
     *
     * @return ApiProblem|mixed
     */
    public function fetchAll()
    {
        $orgId = $this->getEvent()->getRouteParam('org', false);
        return new OrgUsersCollection($this->groupService->fetchUsersForOrg($orgId, [], new UserEntity()));
    }
}
