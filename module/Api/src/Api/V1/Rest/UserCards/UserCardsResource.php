<?php

namespace Api\V1\Rest\UserCards;

use Group\Service\GroupServiceInterface;
use Group\Service\UserCardServiceInterface;
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
    public function fetch($id)
    {
        $groupId = $this->getEvent()->getRouteParam('group_id');
        $group = $this->groupService->fetchGroup($groupId);
        $this->userCardService->generateUserCards($group);
    }
}
