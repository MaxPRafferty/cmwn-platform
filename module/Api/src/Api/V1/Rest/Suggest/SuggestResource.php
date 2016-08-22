<?php
namespace Api\V1\Rest\Suggest;

use Suggest\Service\SuggestedServiceInterface;
use User\UserInterface;
use ZF\ApiProblem\ApiProblem;
use ZF\Rest\AbstractResourceListener;

/**
 * Class SuggestResource
 */
class SuggestResource extends AbstractResourceListener
{
    /**
     * @var SuggestedServiceInterface
     */
    protected $suggestedService;

    /**
     * SuggestResource constructor.
     * @param SuggestedServiceInterface $suggestedFriendService
     */
    public function __construct(SuggestedServiceInterface $suggestedFriendService)
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
        return new SuggestCollection($this->suggestedService->
        fetchSuggestedFriendsForUser($user, null, new SuggestEntity()));
    }
}
