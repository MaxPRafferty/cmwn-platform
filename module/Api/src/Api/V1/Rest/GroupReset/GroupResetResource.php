<?php

namespace Api\V1\Rest\GroupReset;

use Security\Service\SecurityServiceInterface;
use ZF\ApiProblem\ApiProblem;
use ZF\Rest\AbstractResourceListener;

/**
 * Class GroupResetResource
 * @package Api\V1\Rest\GroupReset
 */
class GroupResetResource extends AbstractResourceListener
{
    /**
     * @var SecurityServiceInterface
     */
    protected $service;

    /**
     * GroupResetResource constructor.
     * @param SecurityServiceInterface $securityService
     */
    public function __construct(SecurityServiceInterface $securityService)
    {
        $this->service = $securityService;
    }

    /**
     * @inheritdoc
     */
    public function create($data)
    {
        $code = $this->getInputFilter()->getValue('code');
        $groupId = $this->getEvent()->getRouteParam('group_id');
        $this->service->saveCodeToGroup($code, $groupId);
        return new ApiProblem(200, 'Code reset for group', null, 'Ok');
    }
}
