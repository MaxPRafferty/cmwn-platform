<?php

namespace Api\V1\Rest\UserCards;

use Group\Service\GroupServiceInterface;
use Group\Service\UserCardServiceInterface;
use Zend\Http\Response\Stream;
use ZF\ApiProblem\ApiProblemResponse;
use ZF\Rest\AbstractResourceListener;

/**
 * Resource to fetch user cards pdfs
 */
class UserCardsResource extends AbstractResourceListener
{
    /**
     * @var GroupServiceInterface
     */
    protected $groupService;

    /**
     * @var UserCardServiceInterface
     */
    protected $userCardService;

    /**
     * UserCardsResource constructor.
     * @param GroupServiceInterface $groupService
     * @param UserCardServiceInterface $userCardService
     */
    public function __construct(GroupServiceInterface $groupService, UserCardServiceInterface $userCardService)
    {
        $this->groupService = $groupService;
        $this->userCardService = $userCardService;
    }

    /**
     * @inheritdoc
     */
    public function fetchAll($params = [])
    {
        $groupId = $this->getEvent()->getRouteParam('group_id');
        $group = $this->groupService->fetchGroup($groupId);
        $fileName = $this->userCardService->generateUserCards($group);
        $response = new Stream();
        $response->getHeaders()->addHeaderLine('Content-Type', 'application/pdf');
        $response->getHeaders()->addHeaderLine('Content-Length', filesize($fileName));
        $response->setStream(fopen($fileName, 'r'));
        return $response;
    }
}
