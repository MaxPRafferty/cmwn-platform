<?php
namespace Api\V1\Rest\Suggest;

use Friend\Service\SuggestedFriendServiceInterface;
use User\UserInterface;
use ZF\ApiProblem\ApiProblem;
use ZF\Rest\AbstractResourceListener;

/**
 * Class SuggestResource
 */
class SuggestResource extends AbstractResourceListener
{
    /**
     * @var SuggestedFriendServiceInterface
     */
    protected $suggestedService;

    /**
     * SuggestResource constructor.
     * @param SuggestedFriendServiceInterface $suggestedFriendService
     */
    public function __construct(SuggestedFriendServiceInterface $suggestedFriendService)
    {
        $this->suggestedService = $suggestedFriendService;
    }

    /**
     * Fetch all or a subset of resources
     *
     * @param  array $params
     * @return ApiProblem|mixed
     */
    public function fetchAll($params = [])
    {
        /** @var  UserInterface $user */
        $user = $this->getEvent()->getRouteParam('user');
        return new SuggestCollection($this->suggestedService->fetchSuggestedFriends($user, null, new SuggestEntity()));
    }
}
